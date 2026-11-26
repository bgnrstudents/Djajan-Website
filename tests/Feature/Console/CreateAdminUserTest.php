<?php

namespace Tests\Feature\Console;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CreateAdminUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_account_can_be_created_with_hidden_password_prompts(): void
    {
        $this->artisan('admin:create')
            ->expectsQuestion('Nama admin', 'Admin Djajan')
            ->expectsQuestion('Email admin', 'ADMIN@example.test')
            ->expectsQuestion('Password admin', 'secure-password')
            ->expectsQuestion('Konfirmasi password', 'secure-password')
            ->expectsOutputToContain('Akun admin admin@example.test berhasil dibuat.')
            ->assertExitCode(0);

        $user = User::where('email', 'admin@example.test')->firstOrFail();
        $this->assertSame('Admin Djajan', $user->name);
        $this->assertTrue(Hash::check('secure-password', $user->password));
    }

    public function test_admin_account_command_rejects_an_existing_email(): void
    {
        User::factory()->create(['email' => 'admin@example.test']);

        $this->artisan('admin:create')
            ->expectsQuestion('Nama admin', 'Admin Djajan')
            ->expectsQuestion('Email admin', 'admin@example.test')
            ->expectsQuestion('Password admin', 'secure-password')
            ->expectsQuestion('Konfirmasi password', 'secure-password')
            ->expectsOutputToContain('The email has already been taken.')
            ->assertExitCode(1);

        $this->assertDatabaseCount('users', 1);
    }
}
