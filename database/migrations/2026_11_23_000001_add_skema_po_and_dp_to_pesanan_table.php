<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pesanan', function (Blueprint $table): void {
            if (!Schema::hasColumn('pesanan', 'skema_po')) {
                $table->string('skema_po', 100)->nullable()->after('metode_pembayaran');
            }
            if (!Schema::hasColumn('pesanan', 'nominal_dp')) {
                $table->integer('nominal_dp')->default(0)->after('total_harga');
            }
            if (!Schema::hasColumn('pesanan', 'sisa_bayar')) {
                $table->integer('sisa_bayar')->default(0)->after('nominal_dp');
            }
        });

        if (Schema::hasTable('checkout_groups')) {
            Schema::table('checkout_groups', function (Blueprint $table): void {
                if (!Schema::hasColumn('checkout_groups', 'skema_po')) {
                    $table->string('skema_po', 100)->nullable()->after('metode_pembayaran');
                }
                if (!Schema::hasColumn('checkout_groups', 'nominal_dp')) {
                    $table->integer('nominal_dp')->default(0)->after('total_harga');
                }
                if (!Schema::hasColumn('checkout_groups', 'sisa_bayar')) {
                    $table->integer('sisa_bayar')->default(0)->after('nominal_dp');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('pesanan', function (Blueprint $table): void {
            $table->dropColumn(['skema_po', 'nominal_dp', 'sisa_bayar']);
        });

        if (Schema::hasTable('checkout_groups')) {
            Schema::table('checkout_groups', function (Blueprint $table): void {
                $table->dropColumn(['skema_po', 'nominal_dp', 'sisa_bayar']);
            });
        }
    }
};
