@extends('layouts.app')

@section('title')
勤怠詳細
@endsection

@php
use Carbon\Carbon;
@endphp

@section('css')
<link rel="stylesheet" href="{{asset('css/admin-revision-request.css')}}">
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
<div class="detail-content">
    <h2 class="detail-title">
        勤怠詳細
    </h2>
    <div class="detail-table-wrapper">
        <table class="detail-table">
            <tr class="detail-table-data">
                <th class="detail-table-data-header">
                    名前
                </th>
                <td class="detail-table-data-item">
                    {{$workTime->user->name}}
                </td>
            </tr>
            <tr class="detail-table-data">
                <th class="detail-table-data-header">
                    日付
                </th>
                <td class="detail-table-data-item">
                    <p class="detail-table-data-item-text">
                        {{$workTime->year}}
                    </p>
                    <p class="detail-table-data-item-text">
                        {{$workTime->date_formatted}}
                    </p>
                </td>
            </tr>
            <tr class="detail-table-data">
                <th class="detail-table-data-header">
                    出勤・退勤
                </th>
                <td class="detail-table-data-item">
                    <p class="detail-table-data-item-text">
                        {{$workTime->clock_in_formatted}}
                    </p>
                    <p class="detail-table-data-item-text">
                        ～
                    </p>
                    <p class="detail-table-data-item-text">
                        {{$workTime->clock_out_formatted}}
                    </p>
                </td>
            </tr>
            @foreach($workTime->requestBreaks as $i => $requestBreak)
            <tr class="detail-table-data">
                <th class="detail-table-data-header">
                    {{$i+1 === 1 ? '休憩' : '休憩'. ($i+1)}}
                </th>
                <td class="detail-table-data-item">
                    <p class="detail-table-data-item-text">
                        {{$requestBreak->break_start_formatted}}
                    </p>
                    <p class="detail-table-data-item-text">
                        ～
                    </p>
                    <p class="detail-table-data-item-text">
                        {{$requestBreak->break_end_formatted}}
                    </p>
                </td>
            </tr>
            @endforeach
            <tr class="detail-table-data">
                <th class="detail-table-data-header">
                    備考
                </th>
                <td class="detail-table-data-item">
                    {{$workTime->notes}}
                </td>
            </tr>
        </table>
    </div>
    @if($workTime->status === '承認待ち')
    <div class="detail-form-wrapper">
        <form action="/admin/requests/{{$workTime->id}}" class="detail-form" method="post">
            @csrf
            <div class="detail-table-button-wrapper">
                <button class="detail-table-button" type="submit">
                    承認
                </button>
            </div>
        </form>
    </div>
    @else
    <div class="detail-table-attention-wrapper">
        <p class="detail-table-attention">
            承認済み
        </p>
    </div>
    @endif
</div>
@endsection