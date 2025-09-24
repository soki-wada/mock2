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
            <tr class="request-table-row">
                <td class="request-table-data">
                    {{$unapprovedRequest->status}}
                </td>
                <td class="request-table-data">
                    {{$unapprovedRequest->user->name}}
                </td>
                <td class="request-table-data">
                    {{$unapprovedRequest->attendance->date_formatted}}
                </td>
                <td class="request-table-data">
                    {{$unapprovedRequest->notes}}
                </td>
                <td class="request-table-data">
                    {{$unapprovedRequest->date_formatted}}
                </td>
                <td class="request-table-data">
                    <a href="/admin/requests/{{$unapprovedRequest->id}}" class="request-table-data-item-detail">
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
            <tr class="request-table-row">
                <td class="request-table-data">
                    {{$approvedRequest->status}}
                </td>
                <td class="request-table-data">
                    {{$approvedRequest->user->name}}
                </td>
                <td class="request-table-data">
                    {{$approvedRequest->attendance->date_formatted}}
                </td>
                <td class="request-table-data">
                    {{$approvedRequest->notes}}
                </td>
                <td class="request-table-data">
                    {{$approvedRequest->date_formatted}}
                </td>
                <td class="request-table-data">
                    <a href="/admin/requests/{{$approvedRequest->id}}" class="request-table-data-item-detail">
                        詳細
                    </a>
                </td>
            </tr>
            @endforeach
        </table>
    </div>
</div>
@endsection