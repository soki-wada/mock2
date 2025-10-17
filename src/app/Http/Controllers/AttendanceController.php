<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\WorkRequest;
use App\Models\BreakTime;
use Illuminate\Support\Facades\Auth;





class AttendanceController extends Controller
{
    //
    public function showList(Request $request)
    {
        $user = Auth::user();

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

        $workTimes = Attendance::where('user_id', $user->id)->where('date', 'like', $month->format('Y-m') . '%')->with('breakTimes')->get();

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

        return view('attendance', compact('month', 'days'));
    }

    public function showDetail($id)
    {
        $user = Auth::user();

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

        if (isset($workRequest)) {
            $workRequest->clock_in_formatted = Carbon::parse($workRequest->clock_in)->format('H:i');

            $workRequest->clock_out_formatted = Carbon::parse($workRequest->clock_out)->format('H:i');

            foreach ($workRequest->requestBreaks as $requestBreak) {
                $requestBreak->break_start_formatted = Carbon::parse($requestBreak->break_start)->format('H:i');

                $requestBreak->break_end_formatted = Carbon::parse($requestBreak->break_end)->format('H:i');
            }
        }


        return view('detail', compact('user', 'workTime', 'workRequest'));
    }

    public function showAttendance()
    {
        $user = Auth::user();

        $date =  now()->format('Y年m月d日') . '(' . now()->isoformat('ddd') . ')';
        $time = now()->format('H:i');

        $workTime = Attendance::where('user_id', $user->id)->where('date', 'like', now()->format('Y-m-d') . '%')->with('breakTimes')->first();

        $clock_in = !empty($workTime?->clock_in);

        $clock_out = !empty($workTime?->clock_out);

        $breakTime = $workTime?->breakTimes->last();

        $break_start = !empty($breakTime?->break_start);

        $break_end = !empty($breakTime?->break_end);

        $atWork  = false;
        $atBreak = false;

        if ($break_start && !$break_end) {
            $atBreak = true;
        } else {
            $atWork = true;
        }

        return view('index', compact('date', 'time', 'clock_in', 'clock_out', 'atBreak', 'atWork'));
    }

    public function registerAttendance(Request $request)
    {
        $user = Auth::user();

        $date = now()->format('Y-m-d');

        if ($request->clock_in) {
            Attendance::create([
                'user_id' => $user->id,
                'date' => $date,
                'clock_in' => $request->clock_in
            ]);
        } else {
            $attendance = Attendance::where('user_id', $user->id)->where('date', 'like', $date . '%')->with('breakTimes')->first();

            if ($request->clock_out) {
                Attendance::find($attendance->id)->update([
                    'clock_out' => $request->clock_out
                ]);
            } elseif ($request->break_start) {
                BreakTime::create([
                    'attendance_id' => $attendance->id,
                    'break_start' => $request->break_start,
                ]);
            } elseif ($request->break_end) {
                $break = $attendance->breakTimes()->latest('id')->first();
                BreakTime::find($break->id)->update([
                    'break_end' => $request->break_end
                ]);
            }
        }

        return redirect('/attendance');
    }
}
