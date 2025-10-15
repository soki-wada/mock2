<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Database\Seeders\UsersTableSeeder;
use Database\Seeders\AttendancesTableSeeder;
use Database\Seeders\BreakTimesTableSeeder;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\WorkRequest;
use Carbon\Carbon;


class AdminStaffTest extends TestCase
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

    public function testShowUsers()
    {
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin);

        $response = $this->get('/admin/users');
        $response->assertStatus(200);

        $users = User::where('role', 'user')->get();

        foreach($users as $user){
            $response->assertSee($user->name);
            $response->assertSee($user->email);
        }
    }

    public function testShowWorkTime(){
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin);

        $user = User::find(1);
        $response = $this->get('/admin/users/'. $user->id. '/attendances');
        $response->assertStatus(200);

        $attendances = Attendance::where('user_id', $user->id)->get();

        $response->assertSee($user->name);
        foreach($attendances as $attendance){
            $response->assertSee(Carbon::parse($attendance->clock_in)->format('H:i'));
        }
    }

    public function testLastMonth()
    {
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin);
        $user = User::find(1);
        $response = $this->get('/admin/users/' . $user->id . '/attendances');
        $response->assertStatus(200);


        $currentMonth = Carbon::now()->startOfMonth();
        $response = $this->get('/admin/users/'. $user->id. '/attendances?lastMonth=' . $currentMonth->toDateTimeString());
        $response->assertStatus(200);

        $lastMonth = $currentMonth->copy()->subMonth();
        $response->assertSee($lastMonth->format('Y/m'));
    }

    public function testNextMonth()
    {
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin);
        $user = User::find(1);
        $response = $this->get('/admin/users/' . $user->id . '/attendances');
        $response->assertStatus(200);


        $currentMonth = Carbon::now()->startOfMonth();
        $response = $this->get('/admin/users/' . $user->id . '/attendances?nextMonth=' . $currentMonth->toDateTimeString());
        $response->assertStatus(200);

        $nextMonth = $currentMonth->copy()->addMonth();
        $response->assertSee($nextMonth->format('Y/m'));
    }

    public function testShowDetail(){
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin);
        $user = User::find(1);
        $response = $this->get('/admin/users/' . $user->id . '/attendances');
        $response->assertStatus(200);

        $attendance = Attendance::where('user_id', $user->id)->first();

        $response = $this->get('/admin/attendances/' . $attendance->id);
        $response->assertStatus(200);
        $response->assertSee(Carbon::parse($attendance->date)->format('Y年'));
        $response->assertSee(Carbon::parse($attendance->date)->format('m月d日'));
    }
}
