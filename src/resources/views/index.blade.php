@extends('layouts.app')

@section('title')
勤怠登録画面
@endsection

@php
use Carbon\Carbon;
@endphp

@section('css')
<link rel="stylesheet" href="{{asset('css/index.css')}}">
@endsection

@section('content')
<div class="index-content">
    <div class="index-status-wrapper">
        @if(!$clock_in)
        <p class="index-status">
            勤務外
        </p>
        @elseif($clock_in && $atWork && !$clock_out)
        <p class="index-status">
            出勤中
        </p>
        @elseif($atBreak)
        <p class="index-status">
            休憩中
        </p>
        @else
        <p class="index-status">
            退勤済み
        </p>
        @endif
    </div>
    <div class="index-time-wrapper">
        <p class="index-time-date">
            {{$date}}
        </p>
        <p class="index-time-now">
            {{$time}}
        </p>
    </div>
    <div class="index-form-wrapper">
        @if(!$clock_in)
        <form action="/attendance" class="index-form" method="post">
            @csrf
            <input type="hidden" name="clock_in" value="{{$time}}">
            <div class="index-form-button-wrapper">
                <button class="index-form-button" type="submit">
                    出勤
                </button>
            </div>
        </form>
        @elseif($clock_in && $atWork && !$clock_out)
        <form action="/attendance" class="index-form" method="post">
            @csrf
            <input type="hidden" name="clock_out" value="{{$time}}">
            <div class="index-form-button-wrapper">
                <button class="index-form-button" type="submit">
                    退勤
                </button>
            </div>
        </form>
        <form action="/attendance" class="index-form" method="post">
            @csrf
            <input type="hidden" name="break_start" value="{{$time}}">
            <div class="index-form-button-wrapper">
                <button class="index-form-button" type="submit">
                    休憩入
                </button>
            </div>
        </form>
        @elseif($atBreak)
        <form action="/attendance" class="index-form" method="post">
            @csrf
            <input type="hidden" name="break_end" value="{{$time}}">
            <div class="index-form-button-wrapper">
                <button class="index-form-button" type="submit">
                    休憩戻
                </button>
            </div>
        </form>
        @else
        <p class="index-form-message">
            お疲れ様でした。
        </p>
        @endif
    </div>
</div>
@endsection