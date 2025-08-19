@extends('layouts.app')

@section('title')
会員登録
@endsection

@section('css')
<link rel="stylesheet" href="{{asset('css/register.css')}}">
@endsection

@section('content')
<div class="register-content">
    <div class="register-form-wrapper">
        <form action="/register" method="post" class="register-form" novalidate>
            @csrf
            <h2 class="register-form-title">
                会員登録
            </h2>
            <div class="register-form-item">
                <p class="register-form-item-title">
                    名前
                </p>
                <input type="text" class="register-form-item-input" name="name" value="{{ old('name') }}">
            </div>
            @error('name')
            <p class="error">
                {{$message}}
            </p>
            @enderror
            <div class="register-form-item">
                <p class="register-form-item-title">メールアドレス</p>
                <input type="email" class="register-form-item-input" name="email" value="{{ old('email') }}">
            </div>
            @error('email')
            <p class="error">
                {{$message}}
            </p>
            @enderror
            <div class="register-form-item">
                <p class="register-form-item-title">パスワード</p>
                <input type="password" class="register-form-item-input" name="password">
            </div>
            @error('password')
            <p class="error">
                {{$message}}
            </p>
            @enderror
            <div class="register-form-item">
                <p class="register-form-item-title">パスワード確認</p>
                <input type="password" name="password_confirmation" class="register-form-item-input">
            </div>
            <div class="register-form-button-wrapper">
                <button class="register-form-button" type="submit">
                    登録する
                </button>
            </div>
            <a href="/login" class="register-form-login">
                ログインはこちら
            </a>
        </form>
    </div>
</div>
@endsection