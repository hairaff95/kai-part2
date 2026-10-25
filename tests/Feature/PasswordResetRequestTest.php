<?php

namespace Tests\Feature;

use App\Models\PasswordResetRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordResetRequestTest extends TestCase
{
    use DatabaseTransactions;

    public function test_can_display_password_request_page(): void
    {
        $response = $this->get(route('password.request'));
        $response->assertStatus(200);
        $response->assertSee('Reset Kata Sandi');
    }

    public function test_user_can_submit_password_reset_request(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@kai.id',
            'is_active' => true,
        ]);

        $response = $this->post(route('password.submit-request'), [
            'email' => 'admin@kai.id',
        ]);

        $response->assertRedirect(route('password.request.status', ['email' => 'admin@kai.id']));
        $this->assertDatabaseHas('password_reset_requests', [
            'user_id' => $user->id,
            'status' => 'pending',
        ]);
    }

    public function test_superadmin_can_approve_request(): void
    {
        $superadmin = User::factory()->create([
            'role' => 'superadmin',
            'is_active' => true,
        ]);

        $user = User::factory()->create([
            'email' => 'user@kai.id',
            'is_active' => true,
        ]);

        $resetRequest = PasswordResetRequest::create([
            'user_id' => $user->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($superadmin)->post(route('reset-requests.approve', $resetRequest));

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('password_reset_requests', [
            'id' => $resetRequest->id,
            'status' => 'approved',
        ]);
    }

    public function test_user_can_verify_otp_and_reset_password(): void
    {
        $user = User::factory()->create([
            'email' => 'user@kai.id',
            'password' => Hash::make('OldPassword#123'),
            'is_active' => true,
        ]);

        $resetRequest = PasswordResetRequest::create([
            'user_id' => $user->id,
            'status' => 'approved',
            'otp_code' => '123456',
            'otp_expires_at' => now()->addMinutes(5),
        ]);

        // Verifikasi OTP
        $response = $this->withSession(['reset_request_id' => $resetRequest->id])
            ->post(route('password.verify.post'), [
                'otp' => '123456',
            ]);

        $response->assertRedirect(route('password.reset'));
        $this->assertTrue(session('otp_verified'));

        // Reset Password
        $resetResponse = $this->withSession([
            'otp_verified' => true,
            'reset_request_id' => $resetRequest->id,
            'pending_reset_user_id' => $user->id,
        ])->post(route('password.reset.post'), [
            'password' => 'NewSecurePass#999',
            'password_confirmation' => 'NewSecurePass#999',
        ]);

        $resetResponse->assertRedirect(route('login'));
        $user->refresh();
        $this->assertTrue(Hash::check('NewSecurePass#999', $user->password));
    }

    public function test_superadmin_can_access_settings_page(): void
    {
        $superadmin = User::factory()->create([
            'role' => 'superadmin',
            'is_active' => true,
        ]);

        $response = $this->actingAs($superadmin)->get(route('settings.index'));
        $response->assertStatus(200);
        $response->assertSee('Pengaturan');
        $response->assertSee('Manajemen Admin');
        $response->assertSee('Persetujuan Reset Sandi');
    }

    public function test_superadmin_can_create_new_admin(): void
    {
        $superadmin = User::factory()->create([
            'role' => 'superadmin',
            'is_active' => true,
        ]);

        $response = $this->actingAs($superadmin)->post(route('admins.store'), [
            'name' => 'Budi Santoso',
            'username' => 'budi.santoso',
            'email' => 'budi.santoso@kai.id',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => 'admin',
        ]);

        $response->assertRedirect(route('settings.index', ['tab' => 'manajemen-admin']));
        $this->assertDatabaseHas('users', [
            'username' => 'budi.santoso',
            'email' => 'budi.santoso@kai.id',
            'role' => 'admin',
        ]);
    }
}
