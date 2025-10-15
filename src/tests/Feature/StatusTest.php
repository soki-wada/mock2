<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Database\Seeders\UsersTableSeeder;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\DatabaseMigrations;


class StatusTest extends TestCase
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
    }

    public function testNonWorking()
    {
        $user = User::find(1);
        $this->actingAs($user);

        $response = $this->get('/attendance');
        $response->assertStatus(200);

        $response->assertSee('勤務外');
    }

    public function testWorking()
    {
        $user = User::find(1);
        $this->actingAs($user);

        $date = now()->format('Y-m-d');
        
        Attendance::create([
            'user_id' => $user->id,
            'date' => $date,
            'clock_in' => '9:00'
        ]);

        $response = $this->get('/attendance');
        $response->assertStatus(200);

        $response->assertSee('出勤中');
    }

    public function testBreak()
    {
        $user = User::find(1);
        $this->actingAs($user);

        $date = now()->format('Y-m-d');

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => $date,
            'clock_in' => '9:00'
        ]);

        BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start' => '11:00'
        ]);

        $response = $this->get('/attendance');
        $response->assertStatus(200);

        $response->assertSee('休憩中');
    }

    public function testClockOut()
    {
        $user = User::find(1);
        $this->actingAs($user);

        $date = now()->format('Y-m-d');

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => $date,
            'clock_in' => '9:00',
            'clock_out' => '17:00'
        ]);

        $response = $this->get('/attendance');
        $response->assertStatus(200);

        $response->assertSee('退勤済み');
    }
}
