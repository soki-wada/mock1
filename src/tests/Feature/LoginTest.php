<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    public function testInputEmail()
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('87654321'),
        ]);

        $response = $this->get('/login');
        $response->assertStatus(200);

        $response = $this->post('/login', [
            'email' => '',
            'password' => '87654321',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'メールアドレスを入力してください'
        ]);
    }

    public function testInputPassword()
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('87654321'),
        ]);

        $response = $this->get('/login');
        $response->assertStatus(200);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors([
            'password' => 'パスワードを入力してください'
        ]);
    }

    public function testInputFail()
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('87654321'),
        ]);

        $response = $this->get('/login');
        $response->assertStatus(200);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => '987654321',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'ログイン情報が登録されていません'
        ]);
    }

    public function testInputSuccess()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('87654321'),
        ]);

        $response = $this->get('/login');
        $response->assertStatus(200);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => '87654321',
        ]);

        $this->assertAuthenticatedAs($user);
    }
}
