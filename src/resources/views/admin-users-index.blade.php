@extends('layouts.app')

@section('title')
スタッフ一覧
@endsection

@php
use Carbon\Carbon;
@endphp

@section('css')
<link rel="stylesheet" href="{{asset('css/admin-users-index.css')}}">
@endsection

@section('button')
<div class="header-button-wrapper">
    <a href="/admin/attendances" class="header-button-item">
        勤怠一覧
    </a>
</div>
<div class="header-button-wrapper long">
    <a href="/admin/users" class="header-button-item">
        スタッフ一覧
    </a>
</div>
<div class="header-button-wrapper">
    <a href="/admin/requests" class="header-button-item">
        申請一覧
    </a>
</div>
<div class="header-button-wrapper">
    <form action="/admin/logout" class="header-form-logout" method="post">
        @csrf
        <button class="header-button-item" type="submit">ログアウト</button>
    </form>
</div>
@endsection


@section('content')
<div class="users-content">
    <h2 class="users-title">
        スタッフ一覧
    </h2>
    <div class="users-table-wrapper">
        <table class="users-table">
            <tr class="users-table-header">
                <th class="users-table-header-item">
                    名前
                </th>
                <th class="users-table-header-item">
                    メールアドレス
                </th>
                <th class="users-table-header-item">
                    月次勤怠
                </th>
            </tr>
            @foreach($users as $user)
            <tr class="users-table-data">
                <td class="users-table-data-item">
                    {{$user->name}}
                </td>
                <td class="users-table-data-item">
                    {{$user->email}}
                </td>
                <td class="users-table-data-item">
                    <a href="/admin/users/{{$user->id}}/attendances" class="users-table-data-item detail">
                        詳細
                    </a>
                </td>
            </tr>
            @endforeach
        </table>
    </div>
</div>
@endsection