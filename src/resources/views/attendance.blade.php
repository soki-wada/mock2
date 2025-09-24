@extends('layouts.app')

@section('title')
勤怠一覧
@endsection

@php
use Carbon\Carbon;
@endphp

@section('css')
<link rel="stylesheet" href="{{asset('css/attendance.css')}}">
@endsection

@section('content')
<div class="attendance-content">
    <h2 class="attendance-title">
        勤怠一覧
    </h2>
    <div class="attendance-month">
        <form action="/attendance/list" class="attendance-month-last" method="get">
            <input type="hidden" value="{{$month}}" name="lastMonth">
            <div class="attendance-month-last-button-wrapper">
                <button class="attendance-month-last-button" type="submit">
                    ← 前月
                </button>
            </div>
        </form>
        <div class="attendance-month-current-wrapper">
            <div class="attendance-month-current-image-wrapper">
                <img src="{{asset('images/calendar.png')}}" alt="" class="attendance-month-current-image">
            </div>
            <p class="attendance-month-current-date">
                {{$month->format('Y/m')}}
            </p>
        </div>
        <form action="/attendance/list" class="attendance-month-next" method="get">
            <input type="hidden" value="{{$month}}" name="nextMonth">
            <div class="attendance-month-next-button-wrapper">
                <button class="attendance-month-next-button" type="submit">
                    → 翌月
                </button>
            </div>
        </form>
    </div>
    <div class="attendance-table-wrapper">
        <table class="attendance-table">
            <tr class="attendance-table-header">
                <th class="attendance-table-header-item">
                    日付
                </th>
                <th class="attendance-table-header-item">
                    出勤
                </th>
                <th class="attendance-table-header-item">
                    退勤
                </th>
                <th class="attendance-table-header-item">
                    休憩
                </th>
                <th class="attendance-table-header-item">
                    合計
                </th>
                <th class="attendance-table-header-item">
                    詳細
                </th>
            </tr>
            @foreach($days as $date => $workTime)
            <tr class="attendance-table-data">
                <td class="attendance-table-data-item">
                    {{Carbon::parse($date)->format('m/d'). '('. Carbon::parse($date)->isoformat('ddd'). ')'}}
                </td>
                <td class="attendance-table-data-item">
                    @if(!empty($workTime))
                    {{$workTime->clock_in_formatted}}
                    @endif
                </td>
                <td class="attendance-table-data-item">
                    @if(!empty($workTime->clock_out))
                    {{$workTime->clock_out_formatted}}
                    @endif
                </td>
                <td class="attendance-table-data-item">
                    @if(!empty($workTime->breakTimes))
                    {{$workTime->diff}}
                    @endif
                </td>
                <td class="attendance-table-data-item">
                    @if(!empty($workTime->clock_out))
                    {{$workTime->sum}}
                    @endif
                </td>
                <td class="attendance-table-data-item">
                    @if(!empty($workTime))
                        <a href="/attendance/detail/{{$workTime->id}}" class="attendance-table-data-item-detail">
                            詳細
                        </a>
                    @else
                        <p class="attendance-table-date-item-detail">
                            詳細
                        </p>
                    @endif
                </td>
            </tr>
            @endforeach
        </table>
    </div>
</div>
@endsection