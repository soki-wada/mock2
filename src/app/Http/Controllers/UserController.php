<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\RequestBreak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\WorkRequest;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Carbon\Carbon;
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
        // FormRequest でバリデーション済み
        $validated = $request->validated();

        $user = (new CreateNewUser())->create($validated);

        event(new Registered($user));

        // メール認証通知
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
