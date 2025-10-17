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


class DetailTest extends TestCase
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

    public function testUserName()
    {
        $user = User::find(1);
        $this->actingAs($user);

        $attendance = Attendance::where('user_id', $user->id)->first();

        $response = $this->get('/attendance/detail/' . $attendance->id);
        $response->assertStatus(200);

        $response->assertSee($user->name);
    }

    public function testDate(){
        $user = User::find(1);
        $this->actingAs($user);

        $attendance = Attendance::where('user_id', $user->id)->first();

        $response = $this->get('/attendance/detail/' . $attendance->id);
        $response->assertStatus(200);

        $year = Carbon::parse($attendance->date)->format('Y年');
        $date = Carbon::parse($attendance->date)->format('m月d日');
        $response->assertSee($year);
        $response->assertSee($date);
    }

    public function testClock(){
        $user = User::find(1);
        $this->actingAs($user);

        $attendance = Attendance::where('user_id', $user->id)->first();

        $response = $this->get('/attendance/detail/' . $attendance->id);
        $response->assertStatus(200);

        $clock_in = Carbon::parse($attendance->clock_in)->format('H:i');

        $clock_out = Carbon::parse($attendance->clock_out)->format('H:i');

        $response->assertSee($clock_in);
        $response->assertSee($clock_out);
    }

    public function testBreak(){
        $user = User::find(1);
        $this->actingAs($user);

        $attendance = Attendance::where('user_id', $user->id)->with('breakTimes')->first();

        $response = $this->get('/attendance/detail/' . $attendance->id);
        $response->assertStatus(200);

        foreach($attendance->breakTimes as $breakTime){
            $break_start = Carbon::parse($breakTime->break_start)->format('H:i');

            $break_end = Carbon::parse($breakTime->break_end)->format('H:i');

            $response->assertSee($break_start);
            $response->assertSee($break_end);
        }
    }
}
