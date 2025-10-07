<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkRequest;
use App\Models\RequestBreak;
use Carbon\Carbon;
use App\Models\User;
use App\Models\BreakTime;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;


class RequestController extends Controller
{
    //
    public function modificationRequest(Request $request, $id)
    {
        $user = Auth::user();

        $workRequest = WorkRequest::create([
            'attendance_id' => $id,
            'user_id' => $user->id,
            'status' => '承認待ち',
            'clock_in' => $request->clock_in,
            'clock_out' => $request->clock_out,
            'notes' => $request->notes
        ]);

        foreach ($request->break_start as $i => $start) {
            RequestBreak::create([
                'work_request_id' => $workRequest->id,
                'break_start' => $start,
                'break_end' => $request->break_end[$i]
            ]);
        }

        return redirect('/attendance/detail/' . $id);
    }

    public function showRequest()
    {
        $user = Auth::user();

        $tab = request()->query('tab', 'unapproved');

        $unapprovedRequests = WorkRequest::where('user_id', $user->id)->where('status', '承認待ち')->with('attendance')->get();

        foreach ($unapprovedRequests as $unapprovedRequest) {
            $unapprovedRequest->date_formatted = Carbon::parse($unapprovedRequest->created_at)->format('Y/m/d');

            $unapprovedRequest->attendance->date_formatted = Carbon::parse($unapprovedRequest->attendance->date)->format('Y/m/d');
        }

        $approvedRequests = WorkRequest::where('user_id', $user->id)->where('status', '承認済み')->with('attendance')->get();

        foreach ($approvedRequests as $approvedRequest) {
            $approvedRequest->date_formatted = Carbon::parse($approvedRequest->created_at)->format('Y/m/d');

            $approvedRequest->attendance->date_formatted = Carbon::parse($approvedRequest->attendance->date)->format('Y/m/d');
        }
        
        return view('request', compact('tab', 'unapprovedRequests', 'approvedRequests', 'user'));
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

    public function requestApprove(Request $request, $id)
    {
        $workTime = WorkRequest::with('requestBreaks')->find($id);

        $workTime->update([
            'status' => '承認済み'
        ]);

        Attendance::find($workTime->attendance_id)->update([
            'clock_in' => $workTime->clock_in,
            'clock_out' => $workTime->clock_out,
            'notes' => $workTime->notes
        ]);

        BreakTime::where('attendance_id', $workTime->attendance_id)->delete();

        foreach ($workTime->requestBreaks as $rb) {
            BreakTime::create([
                'attendance_id' => $workTime->attendance_id,
                'break_start'   => $rb->break_start,
                'break_end'     => $rb->break_end,
            ]);
        }
        return redirect('/admin/requests/' . $id);
    }
}
