<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;

    public function testLogout()
    {
        $user = User::factory()->create();

        $this->actingAs($user);
        
        $response = $this->post('/logout');

        $response->assertRedirect('/login');

        $this->assertGuest();
    }
}
