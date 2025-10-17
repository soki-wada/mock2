<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LogoutResponse as LogoutResponseContract;

class LogoutResponse implements LogoutResponseContract
{
    public function toResponse($request)
    {
        $user = $request->user(); // ログアウト直前のユーザー情報

        if ($user && $user->role === 'admin') {
            return redirect('/admin/login');
        }

        return redirect('/login');
    }
}
