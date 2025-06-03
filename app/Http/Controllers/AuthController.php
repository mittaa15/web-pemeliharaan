<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\ResetPasswordMail;
use App\Mail\VerificationMail;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function loginView()
    {
        return view('auth.login');
    }

    public function loginHandler(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return Redirect::back()->withErrors(
                ["message" => "Email atau Password yang dimasukkan salah"]
            );
        }

        // Misal isRead = 1 berarti sudah verifikasi, 0 belum
        if (!$user->isVerified) {
            return Redirect::back()->withErrors(
                ["message" => "Email belum diverifikasi. Silakan cek email."]
            );
        }

        Auth::guard('web')->login($user);

        if ($user->role == 'admin') {
            return redirect('/admin-dashboard');
        }
        if ($user->role == 'user') {
            return redirect('/dashboard');
        }
        if ($user->role == 'sarpras') {
            return redirect('/sarpras-dashboard');
        }
    }


    public function registerView()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users',
                function ($attribute, $value, $fail) {
                    if (!preg_match('/@((student|lecturer)\.itk\.ac\.id)$/', $value)) {
                        $fail('Email harus menggunakan domain itk.');
                    }
                },
            ],
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($validatedData['password']),
            'role' => 'user',
            'isVerified' => false,
            'verification_token' => Str::random(40),
        ]);
        Mail::to($user->email)->send(new VerificationMail($user));

        return redirect('/')->with('success', 'Registrasi berhasil! Silakan cek email Anda untuk melakukan verifikasi sebelum login.');
    }

    public function verifyEmail($token)
    {
        $user = User::where('verification_token', $token)->first();

        if (!$user) {
            return redirect('/')->with('error', 'Token verifikasi tidak valid.');
        }

        $user->isVerified = true;
        $user->verification_token = null;
        $user->save();

        return redirect('/')->with('success', 'Email berhasil diverifikasi. Silakan login.');
    }


    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $username = User::where('email', $request->email)->first();
        if (!$username) {
            return back()->withErrors(['email' => 'Tidak ada akun yang ditemukan dengan email yang anda masukkan']);
        }

        $token = Password::createToken($username);
        $resetUrl = url(route('password.reset', ['token' => $token, 'email' => $request->email], false));

        Mail::to($request->email)->send(new ResetPasswordMail($resetUrl, $username->nama));
    }
    public function resetPasswordView()
    {
        return view('auth.resetPassword');
    }

    public function forgetPasswordView()
    {
        return view('auth.forgetPassword');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed'
        ]);

        $response = Password::reset($request->only('email', 'password', 'password_confirmation', 'token'), function ($user, $password) {
            $user->forceFill([
                'password' => Hash::make($password)
            ])->save();
        });

        if ($response == Password::PASSWORD_RESET) {
            return redirect('/');
        } else {
            return back()->withInput($request->only('email'))->withErrors(['email' => $response]);
        }
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ], [
            'old_password.required' => 'Password lama wajib diisi.',
            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.min' => 'Password baru minimal 6 karakter.',
            'new_password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json(['message' => 'Password lama salah.'], 422);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['message' => 'Password berhasil diubah.']);
    }


    public function logout()
    {
        Auth::logout(); // logout user dari session
        Session::flush(); // hapus semua session

        return redirect('/')->with('success', 'Berhasil logout');
    }
}
