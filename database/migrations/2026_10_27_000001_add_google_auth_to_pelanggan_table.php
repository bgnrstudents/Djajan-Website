<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pelanggan', function (Blueprint $table) {
            $table->string('google_sub')->nullable()->unique()->after('email');
            $table->string('auth_provider')->default('password')->after('google_sub');
            $table->timestamp('email_verified_at')->nullable()->after('auth_provider');
            $table->string('foto_profil')->nullable()->after('email_verified_at');
        });
    }

    public function down(): void
    {
        Schema::table('pelanggan', function (Blueprint $table) {
            $table->dropUnique(['google_sub']);
            $table->dropColumn([
                'google_sub',
                'auth_provider',
                'email_verified_at',
                'foto_profil',
            ]);
        });
    }
};
