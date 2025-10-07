@extends('layouts.app')

@section('title')
管理者申請一覧
@endsection

@php
use Carbon\Carbon;
@endphp

@section('css')
<link rel="stylesheet" href="{{asset('css/admin-request.css')}}">
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
<div class="request-content">
    <h2 class="request-title">
        申請一覧
    </h2>
    <div class="request-section-tab">
        <a href="/admin/requests?tab=unapproved" class="request-section-tab-item {{ $tab === 'unapproved' ? 'is-bold' : 'default' }}">
            承認待ち
        </a>
        <a href="/admin/requests?tab=approved" class="request-section-tab-item {{ $tab === 'approved' ? 'is-bold' : 'default' }}">
            承認済み
        </a>
    </div>
    <div class="request-table-wrapper {{ $tab === 'unapproved' ? 'is-visible' : 'is-hidden' }}">
        <table class="request-table">
            <tr class="request-table-header">
                <th class="request-table-header-item">
                    状態
                </th>
                <th class="request-table-header-item">
                    名前
                </th>
                <th class="request-table-header-item">
                    対象日時
                </th>
                <th class="request-table-header-item">
                    申請理由
                </th>
                <th class="request-table-header-item">
                    申請日時
                </th>
                <th class="request-table-header-item">
                    詳細
                </th>
            </tr>
            @foreach($unapprovedRequests as $unapprovedRequest)
            <tr class="request-table-data">
                <td class="request-table-data-item">
                    {{$unapprovedRequest->status}}
                </td>
                <td class="request-table-data-item">
                    {{$unapprovedRequest->user->name}}
                </td>
                <td class="request-table-data-item">
                    {{$unapprovedRequest->attendance->date_formatted}}
                </td>
                <td class="request-table-data-item">
                    {{$unapprovedRequest->notes}}
                </td>
                <td class="request-table-data-item">
                    {{$unapprovedRequest->date_formatted}}
                </td>
                <td class="request-table-data-item">
                    <a href="/admin/requests/{{$unapprovedRequest->id}}" class="request-table-data-item detail">
                        詳細
                    </a>
                </td>
            </tr>
            @endforeach
        </table>
    </div>
    <div class="request-table-wrapper {{ $tab === 'approved' ? 'is-visible' : 'is-hidden' }}">
        <table class="request-table">
            <tr class="request-table-header">
                <th class="request-table-header-item">
                    状態
                </th>
                <th class="request-table-header-item">
                    名前
                </th>
                <th class="request-table-header-item">
                    対象日時
                </th>
                <th class="request-table-header-item">
                    申請理由
                </th>
                <th class="request-table-header-item">
                    申請日時
                </th>
                <th class="request-table-header-item">
                    詳細
                </th>
            </tr>
            @foreach($approvedRequests as $approvedRequest)
            <tr class="request-table-data">
                <td class="request-table-data-item">
                    {{$approvedRequest->status}}
                </td>
                <td class="request-table-data-item">
                    {{$approvedRequest->user->name}}
                </td>
                <td class="request-table-data-item">
                    {{$approvedRequest->attendance->date_formatted}}
                </td>
                <td class="request-table-data-item">
                    {{$approvedRequest->notes}}
                </td>
                <td class="request-table-data-item">
                    {{$approvedRequest->date_formatted}}
                </td>
                <td class="request-table-data-item">
                    <a href="/admin/requests/{{$approvedRequest->id}}" class="request-table-data-item detail">
                        詳細
                    </a>
                </td>
            </tr>
            @endforeach
        </table>
    </div>
</div>
@endsection