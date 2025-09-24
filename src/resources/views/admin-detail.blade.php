@extends('layouts.app')

@section('title')
勤怠詳細
@endsection

@php
use Carbon\Carbon;
@endphp

@section('css')
<link rel="stylesheet" href="{{asset('css/admin-detail.css')}}">
@endsection

@section('content')
<div class="detail-content">
    <h2 class="detail-title">
        勤怠詳細
    </h2>
    <div class="detail-table-wrapper">
        <form action="/admin/attendances/{{$workTime->id}}" class="detail-form" method="post">
            @csrf
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
                        <input type="time" class="detail-table-data-item-input" name="break_start[{{$breakTime->id}}]" step="60" value="{{old('break_start.'. $breakTime->id, $breakTime->break_start_formatted)}}">
                        <p class="detail-table-data-item-text">
                            ～
                        </p>
                        <input type="time" class="detail-table-data-item-input" name="break_end[{{$breakTime->id}}]" step="60" value="{{old('break_end.'.$breakTime->id, $breakTime->break_end_formatted)}}">
                    </td>
                </tr>
                @endforeach
                <tr class="detail-table-data">
                    <th class="detail-table-data-header">
                        {{count($workTime->breakTimes) === 0 ? '休憩' : '休憩'.(count($workTime->breakTimes)+1)}}
                    </th>
                    <td class="detail-table-data-item">
                        <input type="time" class="detail-table-data-item-input" name="break_start[new]" step="60" value="{{old('break_start.'. 'new')}}">
                        <p class="detail-table-data-item-text">
                            ～
                        </p>
                        <input type="time" class="detail-table-data-item-input" name="break_end[new]" step="60" value="{{old('break_end.'.'new')}}">
                    </td>
                </tr>
                <tr class="detail-table-data">
                    <th class="detail-table-data-header">
                        備考
                    </th>
                    <td class="detail-table-data-item">
                        <textarea name="notes" id="" class="detail-table-data-item-textarea">{{old('notes', $workTime->notes)}}</textarea>
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
</div>
@endsection