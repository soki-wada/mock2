<?php

namespace Tests\Feature;

use Tests\TestCase;
use Database\Seeders\UsersTableSeeder;
use Database\Seeders\AttendancesTableSeeder;
use Database\Seeders\BreakTimesTableSeeder;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\Attendance;
use Carbon\Carbon;

class AdminDetailTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UsersTableSeeder::class);
        $this->seed(AttendancesTableSeeder::class);
        $this->seed(BreakTimesTableSeeder::class);
    }

    public function testShowDetail()
    {
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin);

        $user = User::find(1);
        $attendance = Attendance::where('user_id', $user->id)->where('date', 'like', now()->format('Y-m') . '%')->with('breakTimes')->first();

        $response = $this->get('/admin/attendances/' . $attendance->id);
        $response->assertStatus(200);
        $response->assertSee(Carbon::parse($attendance->date)->format('Y年'));
        $response->assertSee(Carbon::parse($attendance->date)->format('m月d日'));
    }

    public function testClockInError()
    {
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin);

        $user = User::find(1);
        $attendance = Attendance::where('user_id', $user->id)->where('date', 'like', now()->format('Y-m') . '%')->first();

        $response = $this->get('/admin/attendances/' . $attendance->id);
        $response->assertStatus(200);

        $response = $this->followingRedirects()->post('/admin/attendances/' . $attendance->id, [
            'attendance_id' => $attendance->id,
            'user_id' => $user->id,
            'status' => '承認待ち',
            'clock_in' => '12:00',
            'clock_out' => "10:00",
            'notes' => '電車の遅延'
        ]);

        $response->assertSee('出勤時間もしくは退勤時間が不適切な値です');
    }

    public function testBreakStartError()
    {
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin);

        $user = User::find(1);
        $attendance = Attendance::where('user_id', $user->id)->where('date', 'like', now()->format('Y-m') . '%')->first();

        $response = $this->get('/admin/attendances/' . $attendance->id);
        $response->assertStatus(200);

        $response = $this->followingRedirects()->post('/admin/attendances/' . $attendance->id, [
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
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin);

        $user = User::find(1);
        $attendance = Attendance::where('user_id', $user->id)->where('date', 'like', now()->format('Y-m') . '%')->first();

        $response = $this->get('/admin/attendances/' . $attendance->id);
        $response->assertStatus(200);

        $response = $this->followingRedirects()->post('/admin/attendances/' . $attendance->id, [
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
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin);

        $user = User::find(1);
        $attendance = Attendance::where('user_id', $user->id)->where('date', 'like', now()->format('Y-m') . '%')->first();

        $response = $this->get('/admin/attendances/' . $attendance->id);
        $response->assertStatus(200);

        $response = $this->followingRedirects()->post('/admin/attendances/' . $attendance->id, [
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
}
