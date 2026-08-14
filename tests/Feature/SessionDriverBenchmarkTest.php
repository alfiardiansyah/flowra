<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SessionDriverBenchmarkTest extends TestCase
{
    use RefreshDatabase;

    public function test_cookie_session_driver_login_and_dashboard_navigation()
    {
        config(['session.driver' => 'cookie']);

        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $dashboardResponse = $this->get('/dashboard');
        $dashboardResponse->assertStatus(200);
    }

    public function test_file_session_driver_login_and_dashboard_navigation()
    {
        config(['session.driver' => 'file']);

        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $dashboardResponse = $this->get('/dashboard');
        $dashboardResponse->assertStatus(200);
    }
}
