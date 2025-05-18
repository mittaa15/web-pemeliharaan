<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\ResetPasswordMail;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;

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
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($validatedData['password']),
            'role' => 'user',
        ]);

        return redirect('/')->with('success', 'Register berhasil! Silakan login.');
    }
    public function forgetPasswordView()
    {
        return view('auth.forgetPassword');
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

    public function logout()
    {
        Auth::logout(); // logout user dari session
        Session::flush(); // hapus semua session

        return redirect('/')->with('success', 'Berhasil logout');
    }
}