<?php

namespace Tests\Feature;

use Tests\TestCase;
use Database\Seeders\UsersTableSeeder;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\Attendance;


class ClockInTest extends TestCase
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

    public function testClockIn()
    {
        $user = User::find(1);
        $this->actingAs($user);

        $response = $this->get('/attendance');
        $response->assertStatus(200);

        $this->assertMatchesRegularExpression(
            '/<button[^>]*class="index-form-button"[^>]*>\s*出勤\s*<\/button>/',
            $response->getContent()
        );

        $date = now()->format('Y-m-d');

        Attendance::create([
            'user_id' => $user->id,
            'date' => $date,
            'clock_in' => '9:00'
        ]);

        $response = $this->get('/attendance');
        $response->assertSee('出勤中');
    }

    public function testOneClockIn(){
        $user = User::find(1);
        $this->actingAs($user);

        $date = now()->format('Y-m-d');

        Attendance::create([
            'user_id' => $user->id,
            'date' => $date,
            'clock_in' => '9:00',
            'clock_out' => '17:00'
        ]);

        $response = $this->get('/attendance');
        $this->assertDoesNotMatchRegularExpression(
            '/<button[^>]*class="index-form-button"[^>]*>\s*出勤\s*<\/button>/',
            $response->getContent()
        );
    }

    public function testIndex(){
        $user = User::find(1);
        $this->actingAs($user);

        $response = $this->get('/attendance');
        $response->assertStatus(200);

        $date = now()->format('Y-m-d');

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => $date,
            'clock_in' => '9:00'
        ]);

        $response = $this->get('/attendance/list');
        $response->assertSee($attendance->clock_in);
    }
}
