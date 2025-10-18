<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\BreakTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Carbon\Carbon;
use App\Http\Requests\ModificationRequest;

class AdminController extends Controller
{
    //
    public function showUsers()
    {
        $users = User::where('role', 'user')->get();

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

    public function showAdminDetail($id)
    {
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

    public function updateWorkTime(ModificationRequest $request, $id)
    {
        $workTime = Attendance::find($id)->update([
            'clock_in' => $request->clock_in,
            'clock_out' => $request->clock_out,
            'notes' => $request->notes
        ]);

        foreach ($request->break_start as $i => $start) {
            if ($i === 'new' && !empty($start)) {
                BreakTime::create([
                    'attendance_id' => $id,
                    'break_start' => $start,
                    'break_end' => $request->break_end[$i]
                ]);
            } elseif ($i === 'new' && empty($start)) {
            } else {
                BreakTime::find($i)->update([
                    'break_start' => $start,
                    'break_end' => $request->break_end[$i]
                ]);
            }
        }

        return redirect('/admin/attendances/' . $id);
    }

    public function showAdminIndex(Request $request, $user)
    {
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

    public function export(Request $request){
        $user = User::find($request->user_id);

        $month = Carbon::parse($request->date)->format('Y-m');

        $workTimes = Attendance::where('user_id', $user->id)->where('date', 'like', $month . '%')->with('breakTimes')->get();

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

        $monthStart = Carbon::parse($request->date)->startOfMonth();
        $monthEnd   = Carbon::parse($request->date)->endOfMonth();

        $days = [];
        for ($date = $monthStart; $date->lte($monthEnd); $date->addDay()) {
            $days[$date->format('Y-m-d')] = null;
        }

        foreach ($workTimes as $workTime) {
            $days[$workTime->date] = $workTime;
        }

        $csvHeader =[
            '日付', '出勤', '退勤', '休憩', '合計'
        ];
        $temps = [];
        array_push($temps, $csvHeader);

        foreach($days as $date => $workTime){
            $temp = [
                Carbon::parse($date)->format('m/d') . '(' . Carbon::parse($date)->isoformat('ddd') . ')',
                $workTime->clock_in_formatted ?? '',
                $workTime->clock_out_formatted ?? '',
                $workTime->diff ?? '',
                $workTime->sum ?? ''
            ];
            array_push($temps, $temp);
        }
        $stream = fopen('php://temp', 'r+b');
        foreach ($temps as $temp) {
            fputcsv($stream, $temp);
        }
        rewind($stream);
        $csv = str_replace(PHP_EOL, "\r\n", stream_get_contents($stream));
        $csv = mb_convert_encoding($csv, 'SJIS-win', 'UTF-8');
        $filename = $user->name ."さんの勤怠" . "(".Carbon::parse($request->date)->format('Y年m月') . ").csv";
        $headers = array(
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=' . $filename,
        );
        return response()->streamDownload(function () use ($csv) {
            echo $csv;
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        return redirect('/admin/login');
    }
}
