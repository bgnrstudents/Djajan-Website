<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('checkout_groups', function (Blueprint $table): void {
            $table->string('id_checkout')->primary();
            $table->unsignedBigInteger('id_pelanggan');
            $table->string('metode_pembayaran');
            $table->integer('total_harga');
            $table->string('payment_status')->default('pending');
            $table->string('midtrans_order_id')->nullable();
            $table->text('snap_token')->nullable();
            $table->text('payment_url')->nullable();
            $table->dateTime('dibayar_pada')->nullable();
            $table->foreign('id_pelanggan')->references('id_pelanggan')->on('pelanggan')->onDelete('cascade');
        });

        Schema::table('pesanan', function (Blueprint $table): void {
            $table->string('id_checkout')->nullable()->after('id_pesanan');
            $table->foreign('id_checkout')->references('id_checkout')->on('checkout_groups')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pesanan', function (Blueprint $table): void {
            $table->dropForeign(['id_checkout']);
            $table->dropColumn('id_checkout');
        });
        Schema::dropIfExists('checkout_groups');
    }
};
