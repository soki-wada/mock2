<?php

namespace Tests\Feature;

use Tests\TestCase;
use Database\Seeders\UsersTableSeeder;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;


class BreakTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UsersTableSeeder::class);
    }

    public function testBreakButton()
    {
        $user = User::find(1);
        $this->actingAs($user);

        $date = now()->format('Y-m-d');

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => $date,
            'clock_in' => '9:00'
        ]);

        $response = $this->get('/attendance');
        $response->assertStatus(200);

        $this->assertMatchesRegularExpression(
            '/<button[^>]*class="index-form-button white"[^>]*>\s*休憩入\s*<\/button>/',
            $response->getContent()
        );

        BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start' =>  '12:00'
        ]);

        $response = $this->get('/attendance');
        $response->assertSee('休憩中');
    }

    public function testManyBreakStarts(){
        $user = User::find(1);
        $this->actingAs($user);

        $date = now()->format('Y-m-d');

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => $date,
            'clock_in' => '9:00'
        ]);

        $response = $this->get('/attendance');
        $response->assertStatus(200);

        $break = BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start' =>  '12:00'
        ]);

        BreakTime::find($break->id)->update([
            'break_end' => '13:00'
        ]);

        $response = $this->get('/attendance');
        $this->assertMatchesRegularExpression(
            '/<button[^>]*class="index-form-button white"[^>]*>\s*休憩入\s*<\/button>/',
            $response->getContent()
        );
    }

    public function testBreakEndButton(){
        $user = User::find(1);
        $this->actingAs($user);

        $date = now()->format('Y-m-d');

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => $date,
            'clock_in' => '9:00'
        ]);

        $response = $this->get('/attendance');
        $response->assertStatus(200);

        $break = BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start' =>  '12:00'
        ]);

        $response = $this->get('/attendance');
        $this->assertMatchesRegularExpression(
            '/<button[^>]*class="index-form-button white"[^>]*>\s*休憩戻\s*<\/button>/',
            $response->getContent()
        );

        BreakTime::find($break->id)->update([
            'break_end' => '13:00'
        ]);

        $response = $this->get('/attendance');
        $response->assertSee('出勤中');
    }

    public function testManyBreakEnds(){
        $user = User::find(1);
        $this->actingAs($user);

        $date = now()->format('Y-m-d');

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => $date,
            'clock_in' => '9:00'
        ]);

        $response = $this->get('/attendance');

        $break = BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start' =>  '12:00'
        ]);

        BreakTime::find($break->id)->update([
            'break_end' => '13:00'
        ]);

        $second_break = BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start' =>  '14:00'
        ]);

        $response = $this->get('/attendance');
        $this->assertMatchesRegularExpression(
            '/<button[^>]*class="index-form-button white"[^>]*>\s*休憩戻\s*<\/button>/',
            $response->getContent()
        );

        BreakTime::find($second_break->id)->update([
            'break_end' => '15:00'
        ]);

        $response = $this->get('/attendance');
        $response->assertSee('出勤中');
    }

    public function testIndex(){
        $user = User::find(1);
        $this->actingAs($user);

        $date = now()->format('Y-m-d');

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => $date,
            'clock_in' => '9:00'
        ]);

        $response = $this->get('/attendance');

        $break = BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start' =>  '12:00'
        ]);

        BreakTime::find($break->id)->update([
            'break_end' => '13:00'
        ]);
        $breakTime = BreakTime::find($break->id);
        $diff = Carbon::parse($breakTime->break_start)->diffInMinutes(Carbon::parse($breakTime->break_end));
        $hour = floor($diff / 60);
        $minute = $diff % 60;
        $diff = sprintf('%d:%02d', $hour, $minute);

        $response = $this->get('/attendance/list');
        $response->assertSee($diff);
    }
}
