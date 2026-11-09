<?php

namespace App\Console\Commands;

use App\Services\OrderCancellationService;
use Illuminate\Console\Command;

class AutoCancelExpiredOrdersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pesanan:auto-cancel 
                            {--simulate-order= : Simulasi instan untuk id_pesanan tertentu (kebutuhan demo)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Membatalkan pesanan secara otomatis yang melewati batas konfirmasi UMKM (30 mnt Ready Stock / 24 jam PO)';

    /**
     * Execute the console command.
     */
    public function handle(OrderCancellationService $cancellationService): int
    {
        $simulateId = $this->option('simulate-order');

        if ($simulateId) {
            $this->info("Mode simulasi pembatalan otomatis aktif untuk pesanan: {$simulateId}");
        }

        $count = $cancellationService->cancelExpiredOrders($simulateId);

        if ($count === 0) {
            $this->info("Tidak ada pesanan berstatus 'po_baru' yang kedaluwarsa.");
            return Command::SUCCESS;
        }

        $this->info("Berhasil membatalkan {$count} pesanan yang melewati batas konfirmasi.");
        return Command::SUCCESS;
    }
}

