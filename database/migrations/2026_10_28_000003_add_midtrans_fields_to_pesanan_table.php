<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->string('midtrans_order_id')->nullable()->after('status_pembayaran');
            $table->text('snap_token')->nullable()->after('midtrans_order_id');
            $table->text('payment_url')->nullable()->after('snap_token');
            $table->string('payment_status')->default('pending')->after('payment_url');
        });
    }

    public function down(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropColumn(['midtrans_order_id', 'snap_token', 'payment_url', 'payment_status']);
        });
    }
};
