<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pelanggan', function (Blueprint $table): void {
            $table->timestamps();
        });

        Schema::table('umkm', function (Blueprint $table): void {
            $table->timestamp('updated_at')->nullable()->after('created_at');
        });
    }

    public function down(): void
    {
        Schema::table('umkm', function (Blueprint $table): void {
            $table->dropColumn('updated_at');
        });

        Schema::table('pelanggan', function (Blueprint $table): void {
            $table->dropTimestamps();
        });
    }
};
