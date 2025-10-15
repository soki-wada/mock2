<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Database\Seeders\UsersTableSeeder;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\Attendance;
use Carbon\Carbon;


class ClockOutTest extends TestCase
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

    public function testClockOutButton()
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

        $this->assertMatchesRegularExpression(
            '/<button[^>]*class="index-form-button"[^>]*>\s*退勤\s*<\/button>/',
            $response->getContent()
        );

        Attendance::find($attendance->id)->update([
            'clock_out' => '17:00'
        ]);

        $response = $this->get('/attendance');
        $response->assertSee('退勤済');
    }

    public function testIndex(){
        $user = User::find(1);
        $this->actingAs($user);

        $response = $this->get('/attendance');

        $date = now()->format('Y-m-d');

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => $date,
            'clock_in' => '9:00'
        ]);

        Attendance::find($attendance->id)->update([
            'clock_out' => '17:00'
        ]);

        $clock_out = Attendance::find($attendance->id);

        $response = $this->get('/attendance/list');
        $response->assertSee(Carbon::parse($clock_out->clock_out)->format('H:i'));
    }
}
