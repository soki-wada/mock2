<?php

namespace Tests\Feature;

use Tests\TestCase;
use Database\Seeders\UsersTableSeeder;
use Database\Seeders\AttendancesTableSeeder;
use Database\Seeders\BreakTimesTableSeeder;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\Attendance;
use App\Models\RequestBreak;
use App\Models\WorkRequest;
use Carbon\Carbon;

class AdminModificationTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UsersTableSeeder::class);
        $this->seed(AttendancesTableSeeder::class);
        $this->seed(BreakTimesTableSeeder::class);
    }

    public function testShowUnapprovedRequest()
    {
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin);

        $user = User::find(1);
        $attendance = Attendance::where('user_id', $user->id)->with('breakTimes')->first();

        $workRequest = WorkRequest::create([
            'user_id' => $user->id,
            'attendance_id' => $attendance->id,
            'status' => '承認待ち',
            'clock_in' => '10:00',
            'clock_out' => '18:00',
            'notes' => '電車遅延'
        ]);

        RequestBreak::create([
            'work_request_id' => $workRequest->id,
            'break_start' => '11:00',
            'break_end' => '12:00'
        ]);

        $response = $this->get('/admin/requests?tab=unapproved');
        $response->assertStatus(200);
        $response->assertSee($workRequest->status);
        $response->assertSee(Carbon::parse($attendance->date)->format('Y/m/d'));
    }

    public function testShowApprovedRequest()
    {
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin);

        $user = User::find(1);
        $attendance = Attendance::where('user_id', $user->id)->with('breakTimes')->first();

        $workRequest = WorkRequest::create([
            'user_id' => $user->id,
            'attendance_id' => $attendance->id,
            'status' => '承認済み',
            'clock_in' => '10:00',
            'clock_out' => '18:00',
            'notes' => '電車遅延'
        ]);

        RequestBreak::create([
            'work_request_id' => $workRequest->id,
            'break_start' => '11:00',
            'break_end' => '12:00'
        ]);

        $response = $this->get('/admin/requests?tab=approved');
        $response->assertStatus(200);
        $response->assertSee($workRequest->status);
        $response->assertSee(Carbon::parse($attendance->date)->format('Y/m/d'));
    }

    public function testShowRequestDetail()
    {
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin);

        $user = User::find(1);
        $attendance = Attendance::where('user_id', $user->id)->with('breakTimes')->first();

        $workRequest = WorkRequest::create([
            'user_id' => $user->id,
            'attendance_id' => $attendance->id,
            'status' => '承認待ち',
            'clock_in' => '10:00',
            'clock_out' => '18:00',
            'notes' => '電車遅延'
        ]);

        RequestBreak::create([
            'work_request_id' => $workRequest->id,
            'break_start' => '11:00',
            'break_end' => '12:00'
        ]);

        $response = $this->get('/admin/requests/'. $workRequest->id);
        $response->assertStatus(200);
        $response->assertSee($user->name);
        $response->assertSee(Carbon::parse($attendance->date)->format('Y年'));
        $response->assertSee(Carbon::parse($attendance->date)->format('m月d日'));
    }

    public function testApproveRequest()
    {
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin);

        $user = User::find(1);
        $attendance = Attendance::where('user_id', $user->id)->with('breakTimes')->first();

        $workRequest = WorkRequest::create([
            'user_id' => $user->id,
            'attendance_id' => $attendance->id,
            'status' => '承認待ち',
            'clock_in' => '10:00:00',
            'clock_out' => '18:00:00',
            'notes' => '電車遅延'
        ]);

        $breakRequest = RequestBreak::create([
            'work_request_id' => $workRequest->id,
            'break_start' => '11:00:00',
            'break_end' => '12:00:00'
        ]);

        $response = $this->get('/admin/requests/' . $workRequest->id);
        $response->assertStatus(200);
        $response = $this->post('/admin/requests/'. $workRequest->id);

        $this->assertDatabaseHas('attendances', [
            'user_id' => 1,
            'date' => $attendance->date,
            'clock_in' => $workRequest->clock_in,
            'clock_out' => $workRequest->clock_out,
            'notes' => $workRequest->notes
        ]);

        $this->assertDatabaseHas('break_times', [
            'attendance_id' => $attendance->id,
            'break_start' => $breakRequest->break_start,
            'break_end' => $breakRequest->break_end
        ]);
    }
}
