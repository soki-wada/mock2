<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Actions\Fortify\CreateNewUser;
use App\Http\Requests\RegisterRequest;

use App\Http\Requests\LoginRequest;

class UserController extends Controller
{
    //
    public function showAdminLogin(){
        return view('auth.admin-login');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            return redirect('/attendance');
        }

        return back()->withErrors([
            'email' => 'ログイン情報が登録されていません',
        ])->withInput($request->only('email'));
    }

    public function store(RegisterRequest $request)
    {
        $validated = $request->validated();

        $user = (new CreateNewUser())->create($validated);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('verification.notice');
    }

    public function emailAuth()
    {
        return view('auth.email_auth');
    }

    public function assign(){
        $user = Auth::user();

        if ($user && $user->role === 'admin') {
            return redirect('/admin/attendances');
        }
        return redirect('/attendance');
    }

    public function verify(EmailVerificationRequest $request)
    {
        $request->fulfill();
        return redirect('/attendance');
    }

    public function resend(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();
        return back();
    }
}
