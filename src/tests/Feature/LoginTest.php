<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Database\Seeders\UsersTableSeeder;


class LoginTest extends TestCase
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
        $response = $this->get('/login');
        $response->assertStatus(200);

        $response = $this->followingRedirects()->post('/login', [
            'email' => '',
            'password' => '12345678',
        ]);

        $response->assertSee(
            'メールアドレスを入力してください'
        );
    }

    public function testInputPassword()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);

        $response = $this->followingRedirects()->post('/login', [
            'email' => 'yamada@gmail.com',
            'password' => '',
        ]);

        $response->assertSee(
            'パスワードを入力してください'
        );
    }

    public function testInputFail()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);

        $response = $this->followingRedirects()->post('/login', [
            'email' => 'test@example.com',
            'password' => '12345678',
        ]);

        $response->assertSee(
            'ログイン情報が登録されていません'
        );
    }
}
