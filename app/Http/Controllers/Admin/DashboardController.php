<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\Umkm;
use App\Services\AdminOmzetDashboard;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use RuntimeException;
use Symfony\Component\HttpFoundation\HeaderUtils;

class DashboardController extends Controller
{
    public function index(Request $request, AdminOmzetDashboard $dashboard): View
    {
        $filters = $this->validatedFilters($request);
        $analytics = $dashboard->data(
            $filters['mulai'],
            $filters['akhir'],
            $filters['granularity'],
            $filters['search'],
            $filters['page']
        );

        return view('admin.dashboard', [
            'mulai' => $filters['mulai'],
            'akhir' => $filters['akhir'],
            'analytics' => $analytics,
            'reportKey' => (string) Str::uuid(),
            'totalUmkmAktif' => Umkm::where('status_verifikasi', 'terverifikasi')
                ->where('toko_aktif', true)
                ->count(),
            'totalProduk' => Produk::count(),
            'totalPengajuanBaru' => Umkm::whereIn('status_verifikasi', ['pending', 'menunggu'])->count(),
            'totalPengguna' => Umkm::count(),
            'pengajuanTerbaru' => Umkm::whereIn('status_verifikasi', ['pending', 'menunggu'])
                ->latest('created_at')
                ->take(5)
                ->get(),
        ]);
    }

    public function data(Request $request, AdminOmzetDashboard $dashboard): JsonResponse
    {
        $filters = $this->validatedFilters($request);

        return response()->json($dashboard->data(
            $filters['mulai'],
            $filters['akhir'],
            $filters['granularity'],
            $filters['search'],
            $filters['page']
        ));
    }

    public function reportPdf(Request $request, AdminOmzetDashboard $dashboard)
    {
        $filters = $request->validate([
            'periode' => ['required', Rule::in(['harian', 'mingguan', 'bulanan', 'tahunan', 'rentang'])],
            'tanggal' => ['required_unless:periode,rentang', 'nullable', 'date_format:Y-m-d'],
            'mulai' => ['required_if:periode,rentang', 'nullable', 'date_format:Y-m-d'],
            'akhir' => ['required_if:periode,rentang', 'nullable', 'date_format:Y-m-d', 'after_or_equal:mulai'],
            'preview' => ['nullable', 'boolean'],
            'report_key' => ['nullable', 'uuid'],
        ]);

        if ($filters['periode'] === 'rentang') {
            $mulai = $filters['mulai'];
            $akhir = $filters['akhir'];
        } else {
            $tanggal = Carbon::createFromFormat('Y-m-d', $filters['tanggal']);
            [$mulai, $akhir] = match ($filters['periode']) {
                'harian' => [$tanggal->toDateString(), $tanggal->toDateString()],
                'mingguan' => [
                    $tanggal->copy()->startOfWeek(Carbon::MONDAY)->toDateString(),
                    $tanggal->copy()->endOfWeek(Carbon::SUNDAY)->toDateString(),
                ],
                'bulanan' => [
                    $tanggal->copy()->startOfMonth()->toDateString(),
                    $tanggal->copy()->endOfMonth()->toDateString(),
                ],
                'tahunan' => [
                    $tanggal->copy()->startOfYear()->toDateString(),
                    $tanggal->copy()->endOfYear()->toDateString(),
                ],
            };
        }

        $filename = "laporan-omzet-{$mulai}-{$akhir}.pdf";
        $preview = $request->boolean('preview');
        $reportKey = $filters['report_key'] ?? (string) Str::uuid();
        $reportDirectory = storage_path('app/private/omzet-report-cache');
        if (! is_dir($reportDirectory) && ! mkdir($reportDirectory, 0700, true) && ! is_dir($reportDirectory)) {
            throw new RuntimeException('Direktori sementara laporan PDF tidak dapat dibuat.');
        }

        $this->cleanExpiredReports($reportDirectory);
        $cacheKey = hash('sha256', implode('|', [
            (string) $request->user()->getAuthIdentifier(),
            $reportKey,
            $mulai,
            $akhir,
        ]));
        $reportPath = $reportDirectory.DIRECTORY_SEPARATOR.$cacheKey.'.pdf';
        $lock = fopen($reportPath.'.lock', 'c');
        if ($lock === false) {
            throw new RuntimeException('Kunci laporan PDF tidak dapat dibuat.');
        }

        try {
            if (! flock($lock, LOCK_EX)) {
                throw new RuntimeException('Laporan PDF tidak dapat dikunci untuk diproses.');
            }

            if (! is_file($reportPath)) {
                $logoPath = public_path('logo_djajan.png');
                if (! is_file($logoPath) || ($logo = file_get_contents($logoPath)) === false) {
                    throw new RuntimeException('Logo Djajan untuk laporan PDF tidak dapat dibaca.');
                }

                $pdf = Pdf::loadView('admin.reports.omzet', [
                    'report' => $dashboard->report($mulai, $akhir),
                    'mulai' => $mulai,
                    'akhir' => $akhir,
                    'printedAt' => now()->timezone('Asia/Jakarta'),
                    'logo' => 'data:image/png;base64,'.base64_encode($logo),
                ])->setPaper('a4', 'portrait');

                $pdf->render();
                $dompdf = $pdf->getDomPDF();
                $font = $dompdf->getFontMetrics()->getFont('DejaVu Sans', 'normal');
                $dompdf->getCanvas()->page_script(
                    function (int $pageNumber, int $pageCount, $canvas, $fontMetrics) use ($font): void {
                        $text = "Halaman {$pageNumber} dari {$pageCount}";
                        $fontSize = 8;
                        $textWidth = $fontMetrics->getTextWidth($text, $font, $fontSize);
                        $rightMargin = 14 * 72 / 25.4;
                        $x = $canvas->get_width() - $rightMargin - $textWidth;

                        $canvas->text(
                            $x,
                            $canvas->get_height() - 20,
                            $text,
                            $font,
                            $fontSize,
                            [0.08, 0.08, 0.08]
                        );
                    }
                );

                $output = $pdf->output();
                $temporaryPath = tempnam($reportDirectory, 'report-');
                if ($temporaryPath === false) {
                    throw new RuntimeException('Berkas sementara laporan PDF tidak dapat dibuat.');
                }

                try {
                    if (file_put_contents($temporaryPath, $output) !== strlen($output)) {
                        throw new RuntimeException('Berkas sementara laporan PDF tidak dapat ditulis lengkap.');
                    }

                    if (! rename($temporaryPath, $reportPath)) {
                        throw new RuntimeException('Berkas laporan PDF tidak dapat disimpan.');
                    }
                } finally {
                    if (is_file($temporaryPath)) {
                        unlink($temporaryPath);
                    }
                }
            }
        } finally {
            flock($lock, LOCK_UN);
            fclose($lock);
        }

        return response()->file($reportPath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => HeaderUtils::makeDisposition(
                $preview ? 'inline' : 'attachment',
                $filename,
                $filename
            ),
            'Cache-Control' => 'private, no-store, max-age=0',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    private function cleanExpiredReports(string $directory): void
    {
        $expiration = time() - 86400;

        foreach (glob($directory.DIRECTORY_SEPARATOR.'*') ?: [] as $path) {
            $filename = basename($path);
            if (! preg_match('/^[0-9a-f]{64}\.pdf(?:\.lock)?$/i', $filename) || ! is_file($path)) {
                continue;
            }

            $modifiedAt = filemtime($path);
            if ($modifiedAt !== false && $modifiedAt < $expiration && ! unlink($path)) {
                throw new RuntimeException('Berkas laporan PDF lama tidak dapat dibersihkan.');
            }
        }
    }

    private function validatedFilters(Request $request): array
    {
        $filters = $request->validate([
            'mulai' => ['nullable', 'date_format:Y-m-d'],
            'akhir' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:mulai'],
            'granularity' => ['nullable', 'in:day,week,month,year'],
            'search' => ['nullable', 'string', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        return [
            'mulai' => $filters['mulai'] ?? now()->startOfMonth()->toDateString(),
            'akhir' => $filters['akhir'] ?? now()->toDateString(),
            'granularity' => $filters['granularity'] ?? 'day',
            'search' => trim($filters['search'] ?? ''),
            'page' => (int) ($filters['page'] ?? 1),
        ];
    }
}
