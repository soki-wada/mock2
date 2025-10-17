<?php

namespace Tests\Feature;

use Tests\TestCase;
use Database\Seeders\UsersTableSeeder;
use Database\Seeders\AttendancesTableSeeder;
use Database\Seeders\BreakTimesTableSeeder;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\Attendance;
use App\Models\WorkRequest;
use Carbon\Carbon;


class ModificationTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UsersTableSeeder::class);
        $this->seed(AttendancesTableSeeder::class);
        $this->seed(BreakTimesTableSeeder::class);
    }

    public function testClockInError()
    {
        $user = User::find(1);
        $this->actingAs($user);

        $attendance = Attendance::where('user_id', $user->id)->where('date', 'like', now()->format('Y-m') . '%')->first();

        $response = $this->get('/attendance/detail/' . $attendance->id);
        $response->assertStatus(200);

        $response = $this->followingRedirects()->post('/attendance/detail/'. $attendance->id, [
            'attendance_id' => $attendance->id,
            'user_id' => $user->id,
            'status' => '承認待ち',
            'clock_in' => '12:00',
            'clock_out' => "10:00",
            'notes' => '電車の遅延'
        ]);

        $response->assertSee('出勤時間もしくは退勤時間が不適切な値です');
    }

    public function testBreakStartError(){
        $user = User::find(1);
        $this->actingAs($user);

        $attendance = Attendance::where('user_id', $user->id)->where('date', 'like', now()->format('Y-m') . '%')->first();

        $response = $this->get('/attendance/detail/' . $attendance->id);
        $response->assertStatus(200);

        $response = $this->followingRedirects()->post('/attendance/detail/' . $attendance->id, [
            'attendance_id' => $attendance->id,
            'user_id' => $user->id,
            'status' => '承認待ち',
            'clock_in' => '10:00',
            'clock_out' => "17:00",
            'break_start' => ['12:00', '18:00'],
            'break_end' => ['13:00', '16:00'],
            'notes' => '電車の遅延'
        ]);

        $response->assertSee('休憩時間が不適切な値です');
    }

    public function testBreakEndError()
    {
        $user = User::find(1);
        $this->actingAs($user);

        $attendance = Attendance::where('user_id', $user->id)->where('date', 'like', now()->format('Y-m') . '%')->first();

        $response = $this->get('/attendance/detail/' . $attendance->id);
        $response->assertStatus(200);

        $response = $this->followingRedirects()->post('/attendance/detail/' . $attendance->id, [
            'attendance_id' => $attendance->id,
            'user_id' => $user->id,
            'status' => '承認待ち',
            'clock_in' => '10:00',
            'clock_out' => "17:00",
            'break_start' => ['12:00', '15:00'],
            'break_end' => ['13:00', '19:00'],
            'notes' => '電車の遅延'
        ]);

        $response->assertSee('休憩時間もしくは退勤時間が不適切な値です');
    }

    public function testNotesError()
    {
        $user = User::find(1);
        $this->actingAs($user);

        $attendance = Attendance::where('user_id', $user->id)->where('date', 'like', now()->format('Y-m') . '%')->first();

        $response = $this->get('/attendance/detail/' . $attendance->id);
        $response->assertStatus(200);

        $response = $this->followingRedirects()->post('/attendance/detail/' . $attendance->id, [
            'attendance_id' => $attendance->id,
            'user_id' => $user->id,
            'status' => '承認待ち',
            'clock_in' => '10:00',
            'clock_out' => "17:00",
            'break_start' => ['12:00', '15:00'],
            'break_end' => ['13:00', '16:00'],
            'notes' => ''
        ]);

        $response->assertSee('備考を記入してください');
    }

    public function testModificationRequest()
    {
        $user = User::find(1);
        $this->actingAs($user);

        $attendance = Attendance::where('user_id', $user->id)->where('date', 'like', now()->format('Y-m') . '%')->first();

        $response = $this->get('/attendance/detail/' . $attendance->id);
        $response->assertStatus(200);

        $response = $this->followingRedirects()->post('/attendance/detail/' . $attendance->id, [
            'attendance_id' => $attendance->id,
            'user_id' => $user->id,
            'status' => '承認待ち',
            'clock_in' => '10:00',
            'clock_out' => "17:00",
            'break_start' => ['12:00', '15:00'],
            'break_end' => ['13:00', '16:00'],
            'notes' => '電車遅延'
        ]);

        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin);

        $response = $this->get('/admin/requests');
        $response->assertStatus(200);

        $response->assertSee(Carbon::parse($attendance->date)->format('Y/m/d'));

        $modification_request = WorkRequest::where('notes', '電車遅延')->first();

        $response = $this->get('/admin/requests/'. $modification_request->id);
        $response->assertStatus(200);
        $response->assertSee(Carbon::parse($attendance->date)->format('m月d日'));
    }

    public function testUnapprovedRequest(){
        $user = User::find(1);
        $this->actingAs($user);

        $attendance = Attendance::where('user_id', $user->id)->where('date', 'like', now()->format('Y-m') . '%')->first();

        $response = $this->get('/attendance/detail/' . $attendance->id);
        $response->assertStatus(200);

        $response = $this->followingRedirects()->post('/attendance/detail/' . $attendance->id, [
            'attendance_id' => $attendance->id,
            'user_id' => $user->id,
            'status' => '承認待ち',
            'clock_in' => '10:00',
            'clock_out' => "17:00",
            'break_start' => ['12:00', '15:00'],
            'break_end' => ['13:00', '16:00'],
            'notes' => '電車遅延'
        ]);

        $response = $this->get('/stamp_correction_request/list?tab=unapproved' . $attendance->id);
        $response->assertStatus(200);
        $response->assertSee(Carbon::parse($attendance->date)->format('Y/m/d'));
    }

    public function testApprovedRequest(){
        $user = User::find(1);
        $this->actingAs($user);

        $attendance = Attendance::where('user_id', $user->id)->where('date', 'like', now()->format('Y-m') . '%')->first();

        $response = $this->get('/attendance/detail/' . $attendance->id);
        $response->assertStatus(200);

        $response = $this->followingRedirects()->post('/attendance/detail/' . $attendance->id, [
            'attendance_id' => $attendance->id,
            'user_id' => $user->id,
            'status' => '承認待ち',
            'clock_in' => '10:00',
            'clock_out' => "17:00",
            'break_start' => ['12:00', '15:00'],
            'break_end' => ['13:00', '16:00'],
            'notes' => '電車遅延'
        ]);

        WorkRequest::where('attendance_id', $attendance->id)->update([
            'status' => '承認済み'
        ]);

        $response = $this->get('/stamp_correction_request/list?tab=approved' . $attendance->id);
        $response->assertStatus(200);
        $response->assertSee(Carbon::parse($attendance->date)->format('Y/m/d'));
    }

    public function testDetail(){
        $user = User::find(1);
        $this->actingAs($user);

        $attendance = Attendance::where('user_id', $user->id)->where('date', 'like', now()->format('Y-m') . '%')->first();

        $response = $this->get('/attendance/detail/' . $attendance->id);
        $response->assertStatus(200);

        $response = $this->followingRedirects()->post('/attendance/detail/' . $attendance->id, [
            'attendance_id' => $attendance->id,
            'user_id' => $user->id,
            'status' => '承認待ち',
            'clock_in' => '10:00',
            'clock_out' => "17:00",
            'break_start' => ['12:00', '15:00'],
            'break_end' => ['13:00', '16:00'],
            'notes' => '電車遅延'
        ]);

        $workRequest = WorkRequest::where('attendance_id', $attendance->id)->first();

        $response = $this->get('/attendance/detail/' . $workRequest->attendance_id);
        $response->assertStatus(200);
    }
}
