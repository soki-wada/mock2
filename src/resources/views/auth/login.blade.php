@extends('layouts.app')

@section('title')
ログイン
@endsection

@section('css')
<link rel="stylesheet" href="{{asset('css/login.css')}}">
@endsection

@section('content')
<div class="login-content">
    <div class="login-form-wrapper">
        <form action="/login" class="login-form" method="post" novalidate>
            @csrf
            <h2 class="login-form-title">
                ログイン
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
                    ログインする
                </button>
            </div>
        </form>
        <a href="/register" class="login-form-register">
            会員登録はこちら
        </a>
    </div>
</div>
@endsection