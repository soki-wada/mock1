<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
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

        $response = $this->post('/register',[
            'name' => '',
            'email' => 'test@example.com',
            'password' => '87654321',
            'password_confirmation' => '87654321'
        ]);

        $response->assertSessionHasErrors([
            'name' => 'お名前を入力してください'
        ]);
    }

    public function testInputEmail()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);

        $response = $this->post('/register', [
            'name' => '山田太郎',
            'email' => '',
            'password' => '87654321',
            'password_confirmation' => '87654321'
        ]);

        $response->assertSessionHasErrors([
            'email' => 'メールアドレスを入力してください'
        ]);
    }

    public function testInputPassword()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);

        $response = $this->post('/register', [
            'name' => '山田太郎',
            'email' => 'test@example.com',
            'password' => '',
            'password_confirmation' => '87654321'
        ]);

        $response->assertSessionHasErrors([
            'password' => 'パスワードを入力してください'
        ]);
    }

    public function testInputPasswordMin()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);

        $response = $this->post('/register', [
            'name' => '山田太郎',
            'email' => 'test@example.com',
            'password' => '7654321',
            'password_confirmation' => '7654321'
        ]);

        $response->assertSessionHasErrors([
            'password' => 'パスワードは8文字以上で入力してください'
        ]);
    }

    public function testInputPasswordConfirmation()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);

        $response = $this->post('/register', [
            'name' => '山田太郎',
            'email' => 'test@example.com',
            'password' => '87654321',
            'password_confirmation' => '987654321'
        ]);

        $response->assertSessionHasErrors([
            'password' => 'パスワードと一致しません'
        ]);
    }

    public function testRegisterSuccesss()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);

        $response = $this->post('/register', [
            'name' => '山田太郎',
            'email' => 'test@example.com',
            'password' => '87654321',
            'password_confirmation' => '87654321'
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name' => '山田太郎',
        ]);

        $response->assertRedirect('/email/verify');
    }
}
