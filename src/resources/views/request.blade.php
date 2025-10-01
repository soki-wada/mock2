@extends('layouts.app')

@section('title')
申請一覧
@endsection

@php
use Carbon\Carbon;
@endphp

@section('css')
<link rel="stylesheet" href="{{asset('css/request.css')}}">
@endsection

@section('button')
<div class="header-button-wrapper">
    <a href="/attendance" class="header-button-item">
        勤怠
    </a>
</div>
<div class="header-button-wrapper">
    <a href="/attendance/list" class="header-button-item">
        勤怠一覧
    </a>
</div>
<div class="header-button-wrapper">
    <a href="/stamp_correction_request/list" class="header-button-item">
        申請
    </a>
</div>
<div class="header-button-wrapper">
    {{-- @authの予定 --}}
    <form action="/logout" class="header-form-logout" method="post">
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
        <a href="/stamp_correction_request/list?tab=unapproved" class="request-section-tab-item {{ $tab === 'unapproved' ? 'is-bold' : 'default' }}">
            承認待ち
        </a>
        <a href="/stamp_correction_request/list?tab=approved" class="request-section-tab-item {{ $tab === 'approved' ? 'is-bold' : 'default' }}">
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
                    {{$user->name}}
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
                    <a href="/attendance/detail/{{$unapprovedRequest->attendance->id}}" class="request-table-data-item detail">
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
                    {{$user->name}}
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
                    <a href="/attendance/detail/{{$approvedRequest->attendance->id}}" class="request-table-data-item detail">
                        詳細
                    </a>
                </td>
            </tr>
            @endforeach
        </table>
    </div>


</div>
@endsection