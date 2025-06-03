<?php

namespace tests\Unit;

use App\Http\Controllers\AuthController;
use App\Mail\VerificationMail;
use App\Models\User;
use App\Mail\ResetPasswordMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Password;
use Illuminate\Http\RedirectResponse;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

class AuthControllerUnitTest extends TestCase
{
    use RefreshDatabase, WithoutMiddleware;

    public function test_login_handler_returns_redirect_if_user_not_found()
    {
        $request = Request::create('/login', 'POST', [
            'email' => 'notfound@example.com',
            'password' => 'password',
        ]);

        $controller = new AuthController();
        $response = $controller->loginHandler($request);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertTrue(session()->has('errors'));
    }

    public function test_login_handler_redirects_to_dashboard_for_verified_user()
    {
        // Buat user verified di database testing
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'isVerified' => true,
        ]);

        $request = Request::create('/login', 'POST', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        // Mock Hash::check supaya cocok password
        Hash::shouldReceive('check')->andReturn(true);

        $controller = new AuthController();
        $response = $controller->loginHandler($request);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(url('/dashboard'), $response->getTargetUrl());
    }

    public function test_register_sends_verification_email_and_redirects()
    {
        Mail::fake();

        $request = Request::create('/register', 'POST', [
            'name' => 'Test User',
            'email' => 'testtt@student.itk.ac.id',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $controller = new AuthController();
        $response = $controller->register($request);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(url('/'), $response->getTargetUrl());

        Mail::assertSent(VerificationMail::class);
    }

    /** @test */
    public function user_can_register_with_valid_data_and_receive_verification_email()
    {
        // Fake mail supaya tidak benar-benar terkirim
        Mail::fake();

        $response = $this->post(route('register'), [
            'name' => 'Riyadh Abrar',
            'email' => 'riyadh@student.itk.ac.id',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // Pastikan redirect ke homepage
        $response->assertRedirect('/');

        // Pastikan ada session flash success
        $response->assertSessionHas('success', 'Registrasi berhasil! Silakan cek email Anda untuk melakukan verifikasi sebelum login.');

        // Pastikan data user tersimpan di database dengan benar
        $this->assertDatabaseHas('users', [
            'name' => 'Riyadh Abrar',
            'email' => 'riyadh@student.itk.ac.id',
            'role' => 'user',
            'isVerified' => false,
        ]);

        // Ambil user yang baru dibuat
        $user = User::where('email', 'riyadh@student.itk.ac.id')->first();

        $this->assertNotNull($user);

        // Pastikan password terenkripsi dan cocok dengan password asli
        $this->assertTrue(Hash::check('password123', $user->password));

        // Pastikan token verifikasi ada
        $this->assertNotNull($user->verification_token);

        // Pastikan email verifikasi dikirim ke alamat user
        Mail::assertSent(VerificationMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email) && $mail->user->is($user);
        });
    }

    /** @test */
    public function registration_fails_with_invalid_email_domain()
    {
        $response = $this->post(route('register'), [
            'name' => 'Riyadh Abrar',
            'email' => 'riyadh@gmail.com', // domain tidak sesuai
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // Pastikan validasi gagal dan error ada untuk email
        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function registration_fails_when_password_confirmation_does_not_match()
    {
        $response = $this->post(route('register'), [
            'name' => 'Riyadh Abrar',
            'email' => 'riyadh@student.itk.ac.id',
            'password' => 'password123',
            'password_confirmation' => 'password456',
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    /** @test */
    public function test_verify_email_succeeds_with_valid_token_direct_call()
    {
        $user = User::factory()->create([
            'isVerified' => false,
            'verification_token' => 'valid-token-123',
        ]);

        $controller = new AuthController();
        $response = $controller->verifyEmail('valid-token-123');

        // Query ulang user dari database, jangan refresh instance lama
        $updatedUser = User::where('id', $user->id)->first();

        dump($updatedUser->isVerified);
        dump($updatedUser->verification_token);

        $this->assertEquals(1, $updatedUser->isVerified);
        $this->assertNull($updatedUser->verification_token);
        $this->assertInstanceOf(RedirectResponse::class, $response);
    }


    /** @test */
    public function verify_email_fails_with_invalid_token()
    {
        // Panggil route verifyEmail dengan token yang tidak ada
        $response = $this->get('/verify-email/invalid-token-xyz');

        $response->assertRedirect('/');
        $response->assertSessionHas('error', 'Token verifikasi tidak valid.');
    }

    /** @test */
    public function test_send_reset_link_email_fails_if_email_invalid()
    {
        $controller = new AuthController();

        // Buat request dengan email tidak valid
        $request = Request::create('/password/email', 'POST', [
            'email' => 'invalid-email',
        ]);

        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $controller->sendResetLinkEmail($request);
    }

    /** @test */
    public function test_send_reset_link_email_returns_error_if_email_not_found()
    {
        $controller = new AuthController();

        $request = Request::create('/password/email', 'POST', [
            'email' => 'notfound@example.com',
        ]);

        // Karena method return back()->withErrors(), kita cek tipe response dan session errors
        $response = $controller->sendResetLinkEmail($request);

        // Response berupa redirect (Back)
        $this->assertStringContainsString('302', $response->getStatusCode());

        // Pastikan session error ada untuk email
        $this->assertTrue(session()->has('errors'));
        $this->assertArrayHasKey('email', session('errors')->messages());
    }

    /** @test */
    public function test_send_reset_link_email_sends_email_if_user_exists()
    {
        Mail::fake();
        Password::shouldReceive('createToken')->once()->andReturn('dummy-token');

        // Buat user di database testing
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'name' => 'Test User',
        ]);

        $controller = new AuthController();

        $request = Request::create('/password/email', 'POST', [
            'email' => 'user@example.com',
        ]);

        $controller->sendResetLinkEmail($request);

        // Pastikan email terkirim ke alamat yang benar dengan class Mail yang benar
        Mail::assertSent(ResetPasswordMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email)
                && strpos($mail->resetUrl, 'dummy-token') !== false;
        });
    }

    /** @test */
    public function test_reset_password_succeeds_with_valid_data()
    {
        Mail::fake();

        // Buat user
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => bcrypt('oldpassword'),
        ]);

        // Simulasikan token reset
        $token = Password::createToken($user);

        // Kirim request reset password
        $request = Request::create('/reset-password', 'POST', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);

        $controller = new AuthController();
        $response = $controller->resetPassword($request);

        // Refresh user dari DB
        $user->refresh();

        // Password harus sudah diupdate
        $this->assertTrue(Hash::check('newpassword', $user->password));

        // Pastikan redirect ke "/"
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(url('/'), $response->getTargetUrl());
    }

    /** @test */
    public function test_reset_password_fails_with_invalid_token()
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => bcrypt('oldpassword'),
        ]);

        $request = Request::create('/reset-password', 'POST', [
            'token' => 'invalid-token',
            'email' => $user->email,
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);

        $controller = new AuthController();
        $response = $controller->resetPassword($request);

        // Refresh user
        $user->refresh();

        // Password tidak berubah
        $this->assertTrue(Hash::check('oldpassword', $user->password));

        // Pastikan response adalah redirect back
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertTrue(session()->hasOldInput('email')); // input kembali
    }

    public function test_change_password_success()
    {
        // Buat user dummy dan login
        $user = User::factory()->create([
            'password' => bcrypt('oldpassword123'),
        ]);
        $this->actingAs($user);

        // Request data valid
        $response = $this->postJson('/change-password', [
            'old_password' => 'oldpassword123',
            'new_password' => 'newpassword123',
            'new_password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Password berhasil diubah.']);

        $user->refresh();
        $this->assertTrue(Hash::check('newpassword123', $user->password));
    }

    public function test_change_password_fails_with_wrong_old_password()
    {
        $user = User::factory()->create([
            'password' => bcrypt('oldpassword123'),
        ]);
        $this->actingAs($user);

        $response = $this->postJson('/change-password', [
            'old_password' => 'wrongoldpassword',
            'new_password' => 'newpassword123',
            'new_password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(422)
            ->assertJson(['message' => 'Password lama salah.']);

        $user->refresh();
        $this->assertTrue(Hash::check('oldpassword123', $user->password)); // password tidak berubah
    }

    public function test_change_password_fails_validation()
    {
        $user = User::factory()->create([
            'password' => bcrypt('oldpassword123'),
        ]);
        $this->actingAs($user);

        // Password baru kurang dari 6 karakter
        $response = $this->postJson('/change-password', [
            'old_password' => 'oldpassword123',
            'new_password' => '123',
            'new_password_confirmation' => '123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['new_password']);

        // Konfirmasi password tidak cocok
        $response = $this->postJson('/change-password', [
            'old_password' => 'oldpassword123',
            'new_password' => 'newpassword123',
            'new_password_confirmation' => 'differentpassword',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['new_password']);
    }

    public function test_logout_clears_session_and_redirects()
    {
        // Buat user dan login
        $user = User::factory()->create();
        $this->actingAs($user);

        // Simulasikan session dengan data dummy
        session(['my_key' => 'my_value']);
        $this->assertEquals('my_value', session('my_key'));

        // Panggil route logout (sesuaikan route)
        $response = $this->post('/logout');

        // Pastikan user sudah logout
        $this->assertGuest();

        // Pastikan session data custom sudah hilang
        $this->assertNull(session('my_key'));

        // Pastikan diarahkan ke "/" dengan flash message sukses
        $response->assertRedirect('/');
        $response->assertSessionHas('success', 'Berhasil logout');
    }
}