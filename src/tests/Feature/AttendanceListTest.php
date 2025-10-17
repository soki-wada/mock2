<?php

namespace Tests\Feature;

use Tests\TestCase;
use Database\Seeders\UsersTableSeeder;
use Database\Seeders\AttendancesTableSeeder;
use Database\Seeders\BreakTimesTableSeeder;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;


class AttendanceListTest extends TestCase
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

        $userId = 1;
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $attendance = Attendance::create([
                'user_id'   => $userId,
                'date'      => $date->format('Y-m-d'),
                'clock_in'  => '09:00:00',
                'clock_out' => '17:00:00',
            ]);

            // 必要なら休憩データも作る
            BreakTime::create([
                'attendance_id' => $attendance->id,
                'break_start'   => '12:00:00',
                'break_end'     => '13:00:00',
            ]);
        }
    }

    public function testShowList()
    {
        $user = User::find(1);
        $this->actingAs($user);

        $response = $this->get('/attendance/list');

        $startDate = Carbon::now()->subMonth()->startOfMonth();
        $endDate   = Carbon::now()->subMonth()->endOfMonth();

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $attendance = Attendance::where('user_id', $user->id)->where('date', $date->format('Y-m-d'))->first();

            if ($attendance) {
                $clock_in = Carbon::parse($attendance->clock_in)->format('H:i');
                $clock_out = Carbon::parse($attendance->clock_out)->format('H:i');

                $breakMinutes = 0;
                foreach ($attendance->breakTimes as $breakTime) {
                    $breakMinutes += Carbon::parse($breakTime->break_start)->diffInMinutes(Carbon::parse($breakTime->break_end));
                }
                $breakHours = floor($breakMinutes / 60);
                $breakMin   = $breakMinutes % 60;
                $break = sprintf('%d:%02d', $breakHours, $breakMin);

                $workMinutes = Carbon::parse($attendance->clock_in)
                    ->diffInMinutes(Carbon::parse($attendance->clock_out)) - $breakMinutes;
                $workHours = floor($workMinutes / 60);
                $workMin   = $workMinutes % 60;
                $sum = sprintf('%d:%02d', $workHours, $workMin);

                $response->assertSee($clock_in, false);
                $response->assertSee($clock_out, false);
                $response->assertSee($break, false);
                $response->assertSee($sum, false);
            }
        }
    }

    public function testCurrentMonth(){
        $user = User::find(1);
        $this->actingAs($user);

        $currentMonth = Carbon::parse(now())->format('Y/m');

        $response = $this->get('/attendance/list');
        $response->assertSee($currentMonth);
    }

    public function testLastMonth(){
        $user = User::find(1);
        $this->actingAs($user);

        $currentMonth = Carbon::now()->startOfMonth();
        $response = $this->get('/attendance/list');
        $response->assertStatus(200);
        $response = $this->get('/attendance/list?lastMonth=' . $currentMonth->toDateTimeString());
        $response->assertStatus(200);

        $lastMonth = $currentMonth->copy()->subMonth();
        $response->assertSee($lastMonth->format('Y/m'));
    }

    public function testNextMonth(){
        $user = User::find(1);
        $this->actingAs($user);

        $currentMonth = Carbon::now()->startOfMonth();
        $response = $this->get('/attendance/list');
        $response->assertStatus(200);
        $response = $this->get('/attendance/list?nextMonth=' . $currentMonth->toDateTimeString());
        $response->assertStatus(200);

        $nextMonth = $currentMonth->copy()->addMonth();
        $response->assertSee($nextMonth->format('Y/m'));
    }

    public function testDetail(){
        $user = User::find(1);
        $this->actingAs($user);
        $response = $this->get('/attendance/list');

        $attendance = Attendance::where('user_id', $user->id)->first();

        $response = $this->get('/attendance/detail/'. $attendance->id);
        $response->assertStatus(200);

        $year = Carbon::parse($attendance->date)->format('Y年');
        $date = Carbon::parse($attendance->date)->format('m月d日');
        $response->assertSee($year);
        $response->assertSee($date);
    }
}
