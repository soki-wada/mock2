@extends('layouts.app')

@section('title')
勤怠詳細
@endsection

@php
use Carbon\Carbon;
@endphp

@section('css')
<link rel="stylesheet" href="{{asset('css/detail.css')}}">
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
<div class="detail-content">
    <h2 class="detail-title">
        勤怠詳細
    </h2>
    @if(!empty($workRequest))
    <div class="detail-table-wrapper">
        <table class="detail-table">
            <tr class="detail-table-data">
                <th class="detail-table-data-header">
                    名前
                </th>
                <td class="detail-table-data-item">
                    {{$user->name}}
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
                        {{$workRequest->clock_in_formatted}}
                    </p>
                    <p class="detail-table-data-item-text">
                        ～
                    </p>
                    <p class="detail-table-data-item-text">
                        {{$workRequest->clock_out_formatted}}
                    </p>
                </td>
            </tr>
            @foreach($workRequest->requestBreaks as $i => $requestBreak)
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
                    {{$workRequest->notes}}
                </td>
            </tr>
        </table>
    </div>
    <p class="detail-table-attention">
        *承認待ちのため修正はできません。
    </p>
    @else
    <div class="detail-table-wrapper">
        <form action="/attendance/detail/{{$workTime->id}}" class="detail-form" method="post">
            @csrf
            <table class="detail-table">
                <tr class="detail-table-data">
                    <th class="detail-table-data-header">
                        名前
                    </th>
                    <td class="detail-table-data-item">
                        {{$user->name}}
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
                        <input type="time" class="detail-table-data-item-input" name="clock_in" step="60" value="{{old('clock_in', $workTime->clock_in_formatted)}}">
                        <p class="detail-table-data-item-text">
                            ～
                        </p>
                        <input type="time" class="detail-table-data-item-input" name="clock_out" step="60" value="{{old('clock_out', $workTime->clock_out_formatted)}}">
                    </td>
                </tr>
                @foreach($workTime->breakTimes as $i => $breakTime)
                <tr class="detail-table-data">
                    <th class="detail-table-data-header">
                        {{$i+1 === 1 ? '休憩' : '休憩'. ($i+1)}}
                    </th>
                    <td class="detail-table-data-item">
                        <input type="time" class="detail-table-data-item-input" name="break_start[]" step="60" value="{{old('break_start.'. $i, $breakTime->break_start_formatted)}}">
                        <p class="detail-table-data-item-text">
                            ～
                        </p>
                        <input type="time" class="detail-table-data-item-input" name="break_end[]" step="60" value="{{old('break_end.'.$i, $breakTime->break_end_formatted)}}">
                    </td>
                </tr>
                @endforeach
                <tr class="detail-table-data">
                    <th class="detail-table-data-header">
                        {{count($workTime->breakTimes) === 0 ? '休憩' : '休憩'.(count($workTime->breakTimes)+1)}}
                    </th>
                    <td class="detail-table-data-item">
                        <input type="time" class="detail-table-data-item-input" name="break_start[]" step="60" value="{{old('break_start.'. count($workTime->breakTimes))}}">
                        <p class="detail-table-data-item-text">
                            ～
                        </p>
                        <input type="time" class="detail-table-data-item-input" name="break_end[]" step="60" value="{{old('break_end.'.count($workTime->breakTimes))}}">
                    </td>
                </tr>
                <tr class="detail-table-data">
                    <th class="detail-table-data-header">
                        備考
                    </th>
                    <td class="detail-table-data-item">
                        <textarea name="notes" id="" class="detail-table-data-item-textarea"></textarea>
                    </td>
                </tr>
            </table>
            <div class="detail-table-button-wrapper">
                <button class="detail-table-button" type="submit">
                    修正
                </button>
            </div>
        </form>
    </div>
    @endif
</div>
@endsection