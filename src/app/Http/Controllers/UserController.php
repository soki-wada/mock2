<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\BreakTime;
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
            $diff = [];
            foreach($workTime->breakTimes as $breakTime){
                $diff[] = Carbon::parse($breakTime->break_start)->diffInMinutes(Carbon::parse($breakTime->break_end));
            }

            $break = array_sum($diff);
            $hour = floor($break / 60);
            $minute = $break % 60;

            $workTime->diff = sprintf('%d:%02d', $hour, $minute);

            $workTime->clock_in_formatted = Carbon::parse($workTime->clock_in)->format('H:i');

            $workTime->clock_out_formatted = Carbon::parse($workTime->clock_out)->format('H:i');

            $sumMinutes = Carbon::parse($workTime->clock_in)->diffInMinutes(Carbon::parse($workTime->clock_out)) - $break;

            $hour = floor($sumMinutes / 60);
            $minute = $sumMinutes % 60;

            $workTime->sum = sprintf('%d:%02d', $hour, $minute);

            
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
            'user_id' => 1,
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

    public function showRequest(){
        $tab = request()->query('tab', 'unapproved');

        $unapprovedRequests = WorkRequest::where('user_id', 1)->where('status', '承認待ち')->with('attendance')->get();

        foreach($unapprovedRequests as $unapprovedRequest){
            $unapprovedRequest->date_formatted = Carbon::parse($unapprovedRequest->created_at)->format('Y/m/d');

            $unapprovedRequest->attendance->date_formatted = Carbon::parse($unapprovedRequest->attendance->date)->format('Y/m/d');
        }

        $approvedRequests = WorkRequest::where('user_id', 1)->where('status', '承認済み')->with('attendance')->get();

        foreach ($approvedRequests as $approvedRequest) {
            $approvedRequest->date_formatted = Carbon::parse($approvedRequest->created_at)->format('Y/m/d');

            $approvedRequest->attendance->date_formatted = Carbon::parse($approvedRequest->attendance->date)->format('Y/m/d');
        }

        $user = User::find(1);

        return view('request', compact('tab', 'unapprovedRequests', 'approvedRequests', 'user'));
    }

    public function showAttendance(){
        $date =  now()->format('Y年m月d日'). '('. now()->isoformat('ddd').')';
        $time = now()->format('H:i');

        $workTime = Attendance::where('user_id', 1)->where('date', 'like', now()->format('Y-m-d') . '%')->with('breakTimes')->first();

        $clock_in = !empty($workTime?->clock_in);

        $clock_out = !empty($workTime?->clock_out);

        $breakTime = $workTime?->breakTimes->last();

        $break_start = !empty($breakTime?->break_start);

        $break_end = !empty($breakTime?->break_end);

        $atWork  = false;
        $atBreak = false;

        if($break_start && !$break_end){
            $atBreak = true;
        }else{
            $atWork = true;
        }

        return view('index', compact('date', 'time', 'clock_in', 'clock_out', 'atBreak', 'atWork'));
    }

    public function registerAttendance(Request $request){
        $date = now()->format('Y-m-d');

        if($request->clock_in){
            Attendance::create([
                'user_id' => 1,
                'date' => $date,
                'clock_in' => $request->clock_in
            ]);
        }else{
            $attendance = Attendance::where('user_id', 1)->where('date', 'like', $date . '%')->with('breakTimes')->first();

            if($request->clock_out){
                Attendance::find($attendance->id)->update([
                    'clock_out' => $request->clock_out
                ]);
            }elseif($request->break_start){
                BreakTime::create([
                    'attendance_id' => $attendance->id,
                    'break_start' => $request->break_start,
                ]);
            }elseif($request->break_end){
                $break = $attendance->breakTimes()->latest('id')->first();
                BreakTime::find($break->id)->update([
                    'break_end' => $request->break_end
                ]);
            }
        }

        return redirect('/attendance');
    }

    public function showUsers(){
        $users = User::all();

        return view('admin-users-index', compact('users'));
    }

    public function showAdminList(Request $request)
    {
        if ($request->filled('lastDay')) {
            $date = Carbon::parse($request->lastDay)
                ->subDay();
        } elseif ($request->filled('nextDay')) {
            $date = Carbon::parse($request->nextDay)
                ->addDay();
        } else {
            $date = Carbon::now();
        }

        $workTimes = Attendance::where('date', 'like', $date->format('Y-m-d') . '%')->with(['user', 'breakTimes'])->get();

        foreach ($workTimes as $workTime) {
            foreach ($workTime->breakTimes as $breakTime) {
                $diff[] = Carbon::parse($breakTime->break_start)->diffInMinutes(Carbon::parse($breakTime->break_end));
            }

            $break = array_sum($diff);
            $hour = floor($break / 60);
            $minute = $break % 60;

            $workTime->diff = sprintf('%d:%02d', $hour, $minute);

            $workTime->clock_in_formatted = Carbon::parse($workTime->clock_in)->format('H:i');

            $workTime->clock_out_formatted = Carbon::parse($workTime->clock_out)->format('H:i');

            $sumMinutes = Carbon::parse($workTime->clock_in)->diffInMinutes(Carbon::parse($workTime->clock_out)) - $break;

            $hour = floor($sumMinutes / 60);
            $minute = $sumMinutes % 60;

            $workTime->sum = sprintf('%d:%02d', $hour, $minute);
        }

        return view('admin-index', compact('date', 'workTimes'));
    }

    public function showAdminDetail($id){
        $workTime = Attendance::where('id', $id)->with('breakTimes', 'user')->first();

        $workTime->year = Carbon::parse($workTime->date)->format('Y年');

        $workTime->date_formatted = Carbon::parse($workTime->date)->format('m月d日');

        $workTime->clock_in_formatted = Carbon::parse($workTime->clock_in)->format('H:i');

        $workTime->clock_out_formatted = Carbon::parse($workTime->clock_out)->format('H:i');

        foreach ($workTime->breakTimes as $breakTime) {

            $breakTime->break_start_formatted = Carbon::parse($breakTime->break_start)->format('H:i');

            $breakTime->break_end_formatted = Carbon::parse($breakTime->break_end)->format('H:i');
        }

        return view('admin-detail', compact('workTime'));
    }

    public function updateWorkTime(Request $request, $id){
            $workTime = Attendance::find($id)->update([
            'clock_in' => $request->clock_in,
            'clock_out' => $request->clock_out,
            'notes' => $request->notes
        ]);

        foreach ($request->break_start as $i => $start) {
            if($i === 'new' && !empty($start)){
                BreakTime::create([
                    'attendance_id' => $id,
                    'break_start' => $start,
                    'break_end' => $request->break_end[$i]
                ]);
            }elseif($i === 'new' && empty($start)){

            }else{
                BreakTime::find($i)->update([
                    'break_start' => $start,
                    'break_end' => $request->break_end[$i]
                ]);
            }
        }

        return redirect('/admin/attendances/' . $id);
    }

    public function showAdminIndex(Request $request, $user){
        if ($request->filled('lastMonth')) {
            $month = Carbon::parse($request->lastMonth)
                ->subMonth();
        } elseif ($request->filled('nextMonth')) {
            $month = Carbon::parse($request->nextMonth)
                ->addMonth();
        } else {
            $month = Carbon::now()
                ->startOfMonth();
        }

        $workTimes = Attendance::where('user_id', $user)->where('date', 'like', $month->format('Y-m') . '%')->with('breakTimes')->get();

        $staff = User::find($user);

        foreach ($workTimes as $workTime) {
            $diff = [];
            foreach ($workTime->breakTimes as $breakTime) {
                $diff[] = Carbon::parse($breakTime->break_start)->diffInMinutes(Carbon::parse($breakTime->break_end));
            }

            $break = array_sum($diff);
            $hour = floor($break / 60);
            $minute = $break % 60;

            $workTime->diff = sprintf('%d:%02d', $hour, $minute);

            $workTime->clock_in_formatted = Carbon::parse($workTime->clock_in)->format('H:i');

            $workTime->clock_out_formatted = Carbon::parse($workTime->clock_out)->format('H:i');

            $sumMinutes = Carbon::parse($workTime->clock_in)->diffInMinutes(Carbon::parse($workTime->clock_out)) - $break;

            $hour = floor($sumMinutes / 60);
            $minute = $sumMinutes % 60;

            $workTime->sum = sprintf('%d:%02d', $hour, $minute);
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

        return view('admin-attendances-index', compact('month', 'days', 'staff'));
    }

    public function showAdminRequests()
    {
        $tab = request()->query('tab', 'unapproved');

        $unapprovedRequests = WorkRequest::where('status', '承認待ち')->with('attendance', 'user')->get();

        foreach ($unapprovedRequests as $unapprovedRequest) {
            $unapprovedRequest->date_formatted = Carbon::parse($unapprovedRequest->created_at)->format('Y/m/d');

            $unapprovedRequest->attendance->date_formatted = Carbon::parse($unapprovedRequest->attendance->date)->format('Y/m/d');
        }

        $approvedRequests = WorkRequest::where('status', '承認済み')->with('attendance', 'user')->get();

        foreach ($approvedRequests as $approvedRequest) {
            $approvedRequest->date_formatted = Carbon::parse($approvedRequest->created_at)->format('Y/m/d');

            $approvedRequest->attendance->date_formatted = Carbon::parse($approvedRequest->attendance->date)->format('Y/m/d');
        }

        $user = User::find(1);

        return view('admin-request', compact('tab', 'unapprovedRequests', 'approvedRequests'));
    }

    public function showAdminRequestsApproval($id)
    {
        $workTime = WorkRequest::with('requestBreaks', 'attendance')->find($id);

        $workTime->year = Carbon::parse($workTime->attendance->date)->format('Y年');

        $workTime->date_formatted = Carbon::parse($workTime->attendance->date)->format('m月d日');

        $workTime->clock_in_formatted = Carbon::parse($workTime->clock_in)->format('H:i');

        $workTime->clock_out_formatted = Carbon::parse($workTime->clock_out)->format('H:i');

        foreach ($workTime->requestBreaks as $requestBreak) {

            $requestBreak->break_start_formatted = Carbon::parse($requestBreak->break_start)->format('H:i');

            $requestBreak->break_end_formatted = Carbon::parse($requestBreak->break_end)->format('H:i');
        }

        return view('admin-revision-request', compact('workTime'));
    }

    public function requestApprove(Request $request, $id){
        $workTime = WorkRequest::with('requestBreaks')->find($id);

        $workTime->update([
            'status' => '承認済み'
        ]);

        Attendance::find($workTime->attendance_id)->update([
            'clock_in' => $workTime->clock_in,
            'clock_out' => $workTime->clock_out,
            'notes' => $workTime->notes
        ]);

        $breaks = BreakTime::where('attendance_id', $workTime->attendance_id)->get();

        BreakTime::where('attendance_id', $workTime->attendance_id)->delete();

        foreach ($workTime->requestBreaks as $rb) {
            BreakTime::create([
                'attendance_id' => $workTime->attendance_id,
                'break_start'   => $rb->break_start,
                'break_end'     => $rb->break_end,
            ]);
        }
        return redirect('/admin/requests/'. $id);
    }
}
