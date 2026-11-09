<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class MidtransService
{
    public function getTransactionStatus(string $orderId): array
    {
        $serverKey = $this->serverKey();
        if ($serverKey === '') {
            throw new \RuntimeException('MIDTRANS_SERVER_KEY belum diatur di environment.');
        }

        $endpoint = (bool) config('services.midtrans.is_production')
            ? 'https://api.midtrans.com/v2/' . rawurlencode($orderId) . '/status'
            : 'https://api.sandbox.midtrans.com/v2/' . rawurlencode($orderId) . '/status';

        $response = Http::acceptJson()
            ->withBasicAuth($serverKey, '')
            ->get($endpoint);

        if ($response->status() === 404) {
            return [];
        }

        if ($response->failed()) {
            \Log::warning('Midtrans transaction status request failed', [
                'order_id' => $orderId,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \RuntimeException('Gagal mengambil status transaksi Midtrans.');
        }

        $payload = $response->json();
        return is_array($payload) ? $payload : [];
    }

    public function createTransaction(
        string $orderId,
        int $amount,
        array $items,
        array $customer,
        int $expiryHours = 2,
    ): array
    {
        $serverKey = $this->serverKey();
        if ($serverKey === '') {
            throw new \RuntimeException('MIDTRANS_SERVER_KEY belum diatur di environment.');
        }

        $isProduction = (bool) config('services.midtrans.is_production');
        $isSandboxKey = str_starts_with($serverKey, 'SB-Mid-server-');
        $isProductionKey = str_starts_with($serverKey, 'Mid-server-');

        if (!$isProduction && !$isSandboxKey) {
            throw new \RuntimeException(
                'MIDTRANS_IS_PRODUCTION=false membutuhkan Sandbox Server Key '
                . '(diawali SB-Mid-server-).'
            );
        }

        if ($isProduction && !$isProductionKey) {
            throw new \RuntimeException(
                'MIDTRANS_IS_PRODUCTION=true membutuhkan Production Server Key '
                . '(diawali Mid-server-).'
            );
        }

        $endpoint = $isProduction
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';

        $notificationUrl = rtrim(trim((string) config('app.url')), '/')
            . '/api/pesanan/midtrans/callback';

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'X-Override-Notification' => $notificationUrl,
        ])->withBasicAuth($serverKey, '')->post($endpoint, [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $amount,
            ],
            'item_details' => $items,
            'customer_details' => [
                'first_name' => $customer['first_name'] ?? 'Pelanggan',
                'email' => $customer['email'] ?? 'customer@example.com',
                'phone' => $customer['phone'] ?? '',
            ],
            'expiry' => [
                'unit' => 'hour',
                'duration' => $expiryHours,
            ],
            'callbacks' => [
                'finish' => url('/payment/finish?ngrok-skip-browser-warning=true'),
            ],
        ]);

        if ($response->failed()) {
            $message = $response->json('status_message')
                ?? $response->json('error_messages.0')
                ?? 'Gagal membuat transaksi Midtrans.';
            \Log::error('Midtrans create transaction failed', [
                'status' => $response->status(),
                'message' => $message,
                'body' => $response->body(),
            ]);
            throw new \RuntimeException($message);
        }

        $payload = $response->json();
        if (!is_array($payload)) {
            throw new \RuntimeException('Respons Midtrans tidak valid.');
        }

        return [
            'token' => (string) ($payload['token'] ?? ''),
            'redirect_url' => (string) ($payload['redirect_url'] ?? ''),
            'transaction_id' => (string) ($payload['transaction_id'] ?? ''),
        ];
    }

    public function verifyCallback(array $payload, string $serverKey): bool
    {
        $orderId = $this->scalarPayloadValue($payload['order_id'] ?? null);
        $statusCode = $this->scalarPayloadValue($payload['status_code'] ?? null);
        $grossAmount = $this->scalarPayloadValue($payload['gross_amount'] ?? null);
        $signatureKey = strtolower($this->scalarPayloadValue($payload['signature_key'] ?? null));
        $serverKey = trim($serverKey);

        if ($orderId === '' || $statusCode === '' || $grossAmount === '' || $signatureKey === '') {
            \Log::warning('Midtrans verifyCallback: missing fields', [
                'has_order_id'     => $orderId !== '',
                'has_status_code'  => $statusCode !== '',
                'has_gross_amount' => $grossAmount !== '',
                'has_signature'    => $signatureKey !== '',
            ]);
            return false;
        }

        $input    = $orderId . $statusCode . $grossAmount . $serverKey;
        $expected = hash('sha512', $input);

        $match = hash_equals($expected, $signatureKey);

        if (!$match) {
            \Log::warning('Midtrans verifyCallback: signature mismatch', [
                'order_id'       => $orderId,
                'status_code'    => $statusCode,
                'gross_amount'   => $grossAmount,
                'expected_sig'   => substr($expected, 0, 16) . '...',
                'received_sig'   => substr($signatureKey, 0, 16) . '...',
                'server_key_len' => strlen($serverKey),
            ]);
        }

        return $match;
    }

    /**
     * Fallback: verifikasi notifikasi dengan langsung memanggil Midtrans
     * GET /v2/{order_id}/status dan membandingkan transaction_status.
     *
     * Dipakai saat signature lokal gagal (misalnya server key tidak sinkron).
     */
    public function verifyViaApi(string $orderId): array
    {
        $serverKey = $this->serverKey();
        if ($serverKey === '') {
            return [];
        }

        $isProduction = (bool) config('services.midtrans.is_production');

        // Coba Core API
        $endpoint = $isProduction
            ? 'https://api.midtrans.com/v2/' . rawurlencode($orderId) . '/status'
            : 'https://api.sandbox.midtrans.com/v2/' . rawurlencode($orderId) . '/status';

        $response = Http::acceptJson()
            ->withBasicAuth($serverKey, '')
            ->timeout(10)
            ->get($endpoint);

        if ($response->successful()) {
            $data = $response->json();
            return is_array($data) ? $data : [];
        }

        // Jika Core API gagal (403), coba Snap status endpoint
        if ($response->status() === 403) {
            $snapEndpoint = $isProduction
                ? 'https://app.midtrans.com/snap/v1/transactions/' . rawurlencode($orderId) . '/status'
                : 'https://app.sandbox.midtrans.com/snap/v1/transactions/' . rawurlencode($orderId) . '/status';

            $snapResponse = Http::acceptJson()
                ->withBasicAuth($serverKey, '')
                ->timeout(10)
                ->get($snapEndpoint);

            if ($snapResponse->successful()) {
                $data = $snapResponse->json();
                return is_array($data) ? $data : [];
            }
        }

        \Log::warning('Midtrans verifyViaApi failed', [
            'order_id' => $orderId,
            'status'   => $response->status(),
            'body'     => $response->body(),
        ]);

        return [];
    }

    /**
     * Memanggil Midtrans API Refund (POST /v2/{order_id}/refund).
     *
     * Berhasil untuk payment method yang mendukung direct refund (QRIS, Kartu Kredit).
     * Mengembalikan array response dari Midtrans atau array dengan status error jika tidak didukung.
     */
    public function refundTransaction(string $orderId, int $amount, string $reason): array
    {
        $serverKey = $this->serverKey();
        if ($serverKey === '') {
            return [
                'error' => true,
                'message' => 'MIDTRANS_SERVER_KEY belum diatur di environment.',
            ];
        }

        $endpoint = (bool) config('services.midtrans.is_production')
            ? 'https://api.midtrans.com/v2/' . rawurlencode($orderId) . '/refund'
            : 'https://api.sandbox.midtrans.com/v2/' . rawurlencode($orderId) . '/refund';

        $payload = [
            'refund_key' => 'REF-' . $orderId . '-' . time(),
            'amount' => $amount,
            'reason' => $reason,
        ];

        try {
            $response = Http::acceptJson()
                ->withBasicAuth($serverKey, '')
                ->timeout(10)
                ->post($endpoint, $payload);

            \Log::info('Midtrans refund attempt', [
                'order_id' => $orderId,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return is_array($data) ? $data : ['status_code' => (string) $response->status()];
            }

            return [
                'error' => true,
                'status_code' => $response->status(),
                'message' => $response->json('status_message') ?? $response->body(),
            ];
        } catch (\Throwable $e) {
            \Log::warning('Midtrans refund request failed', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
            ]);
            return [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Membatalkan transaksi pending di Midtrans (POST /v2/{order_id}/cancel).
     */
    public function cancelTransaction(string $orderId): array
    {
        $serverKey = $this->serverKey();
        if ($serverKey === '') {
            return [];
        }

        $endpoint = (bool) config('services.midtrans.is_production')
            ? 'https://api.midtrans.com/v2/' . rawurlencode($orderId) . '/cancel'
            : 'https://api.sandbox.midtrans.com/v2/' . rawurlencode($orderId) . '/cancel';

        try {
            $response = Http::acceptJson()
                ->withBasicAuth($serverKey, '')
                ->timeout(10)
                ->post($endpoint);

            \Log::info('Midtrans cancel attempt', [
                'order_id' => $orderId,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return is_array($data) ? $data : [];
            }

            return [];
        } catch (\Throwable $e) {
            \Log::warning('Midtrans cancel request failed', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    private function serverKey(): string
    {
        return trim((string) config('services.midtrans.server_key'));
    }

    private function scalarPayloadValue(mixed $value): string
    {
        return is_scalar($value) ? trim((string) $value) : '';
    }
}
