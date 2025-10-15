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

class AdminModificationTest extends TestCase
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

    public function testShowUnapprovedRequest()
    {
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin);

        $user = User::find(1);
        $attendance = Attendance::where('user_id', $user->id)->where('date', 'like', now()->format('Y-m') . '%')->with('breakTimes')->first();

        $response = $this->get('/admin/requests');
        $response->assertStatus(200);
        $response->assertSee(Carbon::parse($attendance->date)->format('Y年'));
        $response->assertSee(Carbon::parse($attendance->date)->format('m月d日'));
    }

}
