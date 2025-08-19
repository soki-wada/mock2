@extends('layouts.app')

@section('title')
メール認証
@endsection

@section('css')
<link rel="stylesheet" href="{{asset('css/email_auth.css')}}">
@endsection

@section('content')
<div class="email_auth-content">
    <p class="email_auth-message">
        登録していただいたメールアドレスに認証メールを送付しました。<br>
        メール認証を完了してください。
    </p>
    <div class="email_auth-button-wrapper">
        <a href="http://localhost:8025" target="_blank" class="email_auth-button">
            認証はこちらから
        </a>
    </div>
    <form action="{{route('verification.send')}}" class="email_auth-resend-form" method="post">
        @csrf
        <button class="email_auth-resend">
            認証メールを再送する
        </button>
    </form>
</div>
@endsection