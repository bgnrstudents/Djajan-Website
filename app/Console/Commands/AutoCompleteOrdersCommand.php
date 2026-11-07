<?php

namespace App\Console\Commands;

use App\Models\Pesanan;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AutoCompleteOrdersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pesanan:auto-complete 
                            {--hours=24 : Batas jam setelah status diantar untuk diselesaikan otomatis}
                            {--simulate-order= : Simulasi instan untuk id_pesanan tertentu (kebutuhan demo)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menyelesaikan pesanan secara otomatis yang berstatus diantar setelah batas waktu tertentu';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $hours = (int) $this->option('hours');
        if ($hours <= 0) {
            $hours = 24;
        }

        $simulateId = $this->option('simulate-order');

        $query = Pesanan::where('status_pesanan', 'diantar');

        if ($simulateId) {
            $query->where('id_pesanan', $simulateId);
            $this->info("Mode simulasi aktif untuk pesanan: {$simulateId}");
        } else {
            $threshold = now()->subHours($hours);
            $query->where(function ($q) use ($threshold) {
                $q->where('waktu_diantar', '<=', $threshold)
                    ->orWhere(function ($sub) use ($threshold) {
                        $sub->whereNull('waktu_diantar')
                            ->where('tanggal_pesan', '<=', $threshold->toDateString());
                    });
            });
        }

        $orders = $query->get();

        if ($orders->isEmpty()) {
            $this->info("Tidak ada pesanan berstatus 'diantar' yang perlu diselesaikan otomatis.");
            return Command::SUCCESS;
        }

        $count = 0;
        foreach ($orders as $order) {
            DB::transaction(function () use ($order) {
                $order->status_pesanan = 'selesai';
                $order->status_pembayaran = 'lunas';
                $order->waktu_selesai = now();
                if ($order->payment_status === 'cod_pending') {
                    $order->payment_status = 'paid';
                    $order->dibayar_pada = now();
                }
                $order->save();
            });

            $this->line("<info>✓ Selesai otomatis:</info> {$order->id_pesanan} (UMKM: {$order->nama_umkm_snapshot})");
            $count++;
        }

        $this->info("Berhasil menyelesaikan {$count} pesanan secara otomatis.");
        return Command::SUCCESS;
    }
}
