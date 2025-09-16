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
            <tr class="users-table-row">
                <td class="users-table-data">
                    {{$user->name}}
                </td>
                <td class="users-table-data">
                    {{$user->email}}
                </td>
                <td class="users-table-data">
                    <a href="/admin/users/{{$user->id}}/attendances" class="users-table-data-item-detail">
                        詳細
                    </a>
                </td>
            </tr>
            @endforeach
        </table>
    </div>
</div>
@endsection