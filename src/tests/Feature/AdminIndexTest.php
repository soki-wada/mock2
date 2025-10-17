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


class AdminIndexTest extends TestCase
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

    public function testShowIndex()
    {
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin);

        $workTimes = Attendance::where('date', now()->format('Y-m-d'))->with('breakTimes')->get();
        $response = $this->get('/admin/attendances');
        $response->assertStatus(200);
        foreach($workTimes as $workTime){
            $user = User::find($workTime->user_id);
            $response->assertSee($user->name);
        }
    }

    public function testCurrentDate(){
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin);
        $currentDate = Carbon::parse(now())->format('Y年m月d日');
        $response = $this->get('/admin/attendances');
        $response->assertStatus(200);
        $response->assertSee($currentDate);
    }

    public function testLastDay()
    {
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin);

        $currentDate = Carbon::now();
        $response = $this->get('/admin/attendances?lastDay='. $currentDate->toDateTimeString());
        $response->assertStatus(200);

        $lastDay = $currentDate->copy()->subDay();
        $response->assertSee($lastDay->format('Y年m月d日'));
    }

    public function testnextDay()
    {
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin);

        $currentDate = Carbon::now();
        $response = $this->get('/admin/attendances?nextDay=' . $currentDate->toDateTimeString());
        $response->assertStatus(200);

        $nextDay = $currentDate->copy()->addDay();
        $response->assertSee($nextDay->format('Y年m月d日'));
    }
}
