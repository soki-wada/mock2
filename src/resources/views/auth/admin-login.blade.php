@extends('layouts.app')

@section('title')
管理者ログイン
@endsection

@section('css')
<link rel="stylesheet" href="{{asset('css/admin-login.css')}}">
@endsection

@section('content')
<div class="login-content">
    <div class="login-form-wrapper">
        <form action="/login" class="login-form" method="post" novalidate>
            @csrf
            <input type="hidden" name="login_as" value="admin">
            <h2 class="login-form-title">
                管理者ログイン
            </h2>
            <div class="login-form-item">
                <p class="login-form-item-title">メールアドレス</p>
                <input type="email" class="login-form-item-input" name="email" value="{{old('email')}}">
            </div>
            @error('email')
            <p class="error">
                {{$message}}
            </p>
            @enderror
            <div class="login-form-item">
                <p class="login-form-item-title">パスワード</p>
                <input type="password" class="login-form-item-input" name="password">
            </div>
            @error('password')
            <p class="error">
                {{$message}}
            </p>
            @enderror
            <div class="login-form-button-wrapper">
                <button class="login-form-button" type="submit">
                    管理者ログインする
                </button>
            </div>
        </form>
    </div>
</div>
@endsection