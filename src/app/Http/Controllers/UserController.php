<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\RequestBreak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\WorkRequest;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Carbon\Carbon;


class UserController extends Controller
{
    //
    public function showAdminLogin(){
        return view('auth.admin-login');
    }

    public function emailAuth()
    {
        return view('auth.email_auth');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            return redirect('/');
        }

        return back()->withErrors([
            'email' => 'ログイン情報が登録されていません',
        ])->withInput($request->only('email'));
    }

    public function storeUser(Request $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);
        Auth::login($user);
        event(new Registered($user));
        return redirect()->route('verification.notice');
    }

    public function verify(EmailVerificationRequest $request)
    {
        $request->fulfill();
        return redirect('/mypage/profile');
    }

    public function resend(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();
        return back();
    }

    public function showList(Request $request){
        if($request->filled('lastMonth')){
            $month = Carbon::parse($request->lastMonth)
                ->subMonth();
        }elseif($request->filled('nextMonth')){
            $month = Carbon::parse($request->nextMonth)
                ->addMonth();
        }else{
            $month = Carbon::now()
                ->startOfMonth();
        }

        $workTimes = Attendance::where('user_id', 1)->where('date', 'like', $month->format('Y-m'). '%')->with('breakTimes')->get();

        foreach ($workTimes as $workTime){
            foreach($workTime->breakTimes as $breakTime){
                $diff = Carbon::parse($breakTime->break_start)->diffInMinutes(Carbon::parse($breakTime->break_end));

                $hour = floor($diff / 60);
                $minute = $diff % 60;

                $breakTime->diff = sprintf('%d:%02d', $hour, $minute);

                $workTime->clock_in_formatted = Carbon::parse($workTime->clock_in)->format('H:i');

                $workTime->clock_out_formatted = Carbon::parse($workTime->clock_out)->format('H:i');

                $sumMinutes = Carbon::parse($workTime->clock_in)->diffInMinutes(Carbon::parse($workTime->clock_out)) - $diff;

                $hour = floor($sumMinutes / 60);
                $minute = $sumMinutes % 60;

                $workTime->sum = sprintf('%d:%02d', $hour, $minute);
            }
        }

        $monthStart = Carbon::parse($month)->startOfMonth();
        $monthEnd   = Carbon::parse($month)->endOfMonth();

        $days = [];
        for ($date = $monthStart; $date->lte($monthEnd); $date->addDay()) {
            $days[$date->format('Y-m-d')] = null;
        }

        foreach ($workTimes as $workTime) {
            $days[$workTime->date] = $workTime;
        }

        return view('attendance', compact('month', 'days'));
    }

    public function showDetail($id){
        $user = User::find(1);

        $workTime = Attendance::with('breakTimes')->find($id);

        $workTime->year = Carbon::parse($workTime->date)->format('Y年');

        $workTime->date_formatted = Carbon::parse($workTime->date)->format('m月d日');

        $workTime->clock_in_formatted = Carbon::parse($workTime->clock_in)->format('H:i');

        $workTime->clock_out_formatted = Carbon::parse($workTime->clock_out)->format('H:i');

        foreach ($workTime->breakTimes as $breakTime) {

            $breakTime->break_start_formatted = Carbon::parse($breakTime->break_start)->format('H:i');

            $breakTime->break_end_formatted = Carbon::parse($breakTime->break_end)->format('H:i');
        }

        $workRequest = WorkRequest::with('requestBreaks')->where('attendance_id', $id)->where('status', '承認待ち')->first();

        if(isset($workRequest)){
            $workRequest->clock_in_formatted = Carbon::parse($workRequest->clock_in)->format('H:i');

            $workRequest->clock_out_formatted = Carbon::parse($workRequest->clock_out)->format('H:i');

            foreach ($workRequest->requestBreaks as $requestBreak) {
                $requestBreak->break_start_formatted = Carbon::parse($requestBreak->break_start)->format('H:i');

                $requestBreak->break_end_formatted = Carbon::parse($requestBreak->break_end)->format('H:i');
            }
        }


        return view('detail', compact('user', 'workTime', 'workRequest'));
    }

    public function modificationRequest(Request $request, $id){
        $workRequest = WorkRequest::create([
            'attendance_id' => $id,
            'status' => '承認待ち',
            'clock_in' => $request->clock_in,
            'clock_out' => $request->clock_out,
            'notes' => $request->notes
        ]);

        foreach($request->break_start as $i => $start){
        RequestBreak::create([
                'work_request_id' => $workRequest->id,
                'break_start' => $start,
                'break_end' => $request->break_end[$i]
            ]);
        }

        return redirect('/attendance/detail/'. $id);
    }
}
