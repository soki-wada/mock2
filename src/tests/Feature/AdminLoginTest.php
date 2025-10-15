<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Database\Seeders\UsersTableSeeder;


class AdminLoginTest extends TestCase
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


    public function testInputEmail()
    {
        $response = $this->get('/admin/login');
        $response->assertStatus(200);

        $response = $this->followingRedirects()->post('/login', [
            'email' => '',
            'password' => '87654321',
        ]);

        $response->assertSee(
            'メールアドレスを入力してください'
        );
    }

    public function testInputPassword()
    {
        $response = $this->get('/admin/login');
        $response->assertStatus(200);

        $response = $this->followingRedirects()->post('/login', [
            'email' => 'sato@gmail.com',
            'password' => '',
        ]);

        $response->assertSee(
            'パスワードを入力してください'
        );
    }

    public function testInputFail()
    {
        $response = $this->get('/admin/login');
        $response->assertStatus(200);

        $response = $this->followingRedirects()->post('/login', [
            'email' => 'test@example.com',
            'password' => '87654321',
        ]);

        $response->assertSee(
            'ログイン情報が登録されていません'
        );
    }
}
