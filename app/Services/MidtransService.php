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

    public function createTransaction(string $orderId, int $amount, array $items, array $customer): array
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
            return false;
        }

        $expected = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        return hash_equals($expected, $signatureKey);
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
