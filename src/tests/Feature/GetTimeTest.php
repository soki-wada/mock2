<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Database\Seeders\UsersTableSeeder;
use App\Models\User;

class GetTimeTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UsersTableSeeder::class);
    }

    public function testGetTime()
    {
        $user = User::find(1);
        $this->actingAs($user);

        $response = $this->get('/attendance');
        $response->assertStatus(200);

        $date = now()->format('Y年m月d日') . '(' . now()->isoformat('ddd') . ')';
        $time = now()->format('H:i');

        $response->assertSee($date);
        $response->assertSee($time);
    }
}
