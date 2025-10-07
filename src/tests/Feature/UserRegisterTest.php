<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserRegisterTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    public function testInputName()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);

        $response = $this->followingRedirects()->post('/register', [
            'name' => '',
            'email' => 'test@gmail.com',
            'password' => '11111111',
            'password_confirmation' => '11111111'
        ]);

        $response->assertSee(
            'お名前を入力してください'
        );
    }

    public function testInputEmail()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);

        $response = $this->followingRedirects()->post('/register', [
            'name' => '鈴木一郎',
            'email' => '',
            'password' => '11111111',
            'password_confirmation' => '11111111'
        ]);

        $response->assertSee(
            'メールアドレスを入力してください'
        );
    }

    public function testInputPassword()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);

        $response = $this->followingRedirects()->post('/register', [
            'name' => '鈴木一郎',
            'email' => 'test@gmail.com',
            'password' => '',
            'password_confirmation' => '11111111'
        ]);

        $response->assertSee([
            'パスワードを入力してください'
        ]);
    }

    public function testInputPasswordMin()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);

        $response = $this->followingRedirects()->post('/register', [
            'name' => '鈴木一郎',
            'email' => 'test@example.com',
            'password' => '1111111',
            'password_confirmation' => '11111111'
        ]);

        $response->assertSee([
            'パスワードは8文字以上で入力してください'
        ]);
    }

    public function testInputPasswordConfirmation()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);

        $response = $this->followingRedirects()->post('/register', [
            'name' => '鈴木一郎',
            'email' => 'test@example.com',
            'password' => '11111111',
            'password_confirmation' => '111111111'
        ]);

        $response->assertSee([
            'パスワードと一致しません'
        ]);
    }

    public function testRegisterSuccesss()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);

        $response = $this->post('/register', [
            'name' => '鈴木一郎',
            'email' => 'test@example.com',
            'password' => '11111111',
            'password_confirmation' => '11111111'
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name' => '鈴木一郎',
        ]);

        $response->assertRedirect('/email/verify');
    }
}
