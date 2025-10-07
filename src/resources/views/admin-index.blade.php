@extends('layouts.app')

@section('title')
勤怠一覧
@endsection

@php
use Carbon\Carbon;
@endphp

@section('css')
<link rel="stylesheet" href="{{asset('css/admin-index.css')}}">
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
<div class="index-content">
    <h2 class="index-title">
        {{$date->format('Y年m月d日')}}の勤怠
    </h2>
    <div class="index-date">
        <form action="/admin/attendances" class="index-date-last" method="get">
            <input type="hidden" value="{{$date}}" name="lastDay">
            <div class="index-date-last-button-wrapper">
                <button class="index-date-last-button" type="submit">
                    ← 前日
                </button>
            </div>
        </form>
        <div class="index-date-current-wrapper">
            <div class="index-date-current-image-wrapper">
                <img src="{{asset('images/calendar.png')}}" alt="" class="index-date-current-image">
            </div>
            <p class="index-date-current-date">
                {{$date->format('Y/m/d')}}
            </p>
        </div>
        <form action="/admin/attendances" class="index-date-next" method="get">
            <input type="hidden" value="{{$date}}" name="nextDay">
            <div class="index-date-next-button-wrapper">
                <button class="index-date-next-button" type="submit">
                    翌日 →
                </button>
            </div>
        </form>
    </div>

    <div class="index-table-wrapper">
        <table class="index-table">
            <tr class="index-table-header">
                <th class="index-table-header-item">
                    名前
                </th>
                <th class="index-table-header-item">
                    出勤
                </th>
                <th class="index-table-header-item">
                    退勤
                </th>
                <th class="index-table-header-item">
                    休憩
                </th>
                <th class="index-table-header-item">
                    合計
                </th>
                <th class="index-table-header-item">
                    詳細
                </th>
            </tr>
            @foreach($workTimes as $workTime)
            <tr class="index-table-data">
                <td class="index-table-data-item">
                    {{$workTime->user->name}}
                </td>
                <td class="index-table-data-item">
                    {{$workTime->clock_in_formatted}}
                </td>
                <td class="index-table-data-item">
                    @if(!empty($workTime->clock_out))
                    {{$workTime->clock_out_formatted}}
                    @endif
                </td>
                <td class="index-table-data-item">
                    @if(!empty($workTime->breakTimes))
                    {{$workTime->diff}}
                    @endif
                </td>
                <td class="index-table-data-item">
                    @if(!empty($workTime->clock_out))
                    {{$workTime->sum}}
                    @endif
                </td>
                <td class="index-table-data-item">
                    <a href="/admin/attendances/{{$workTime->id}}" class="users-table-data-item detail">
                        詳細
                    </a>
                </td>
            </tr>
            @endforeach
        </table>
    </div>
</div>
@endsection