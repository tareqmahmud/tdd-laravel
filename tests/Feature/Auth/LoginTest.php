<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public string $email = 'test@example.com';
    public string $password = 'TestExample1234$';

    protected function setUp(): void
    {
        parent::setUp();

        // Register a user first
        User::factory()->create([
            'email'    => $this->email,
            'password' => $this->password
        ]);
    }

    public function test_a_user_can_login(): void
    {
        $response = $this->postJson(route('auth.login'), [
            'email'    => $this->email,
            'password' => $this->password
        ]);

        $response->assertOk();

        $this->assertArrayHasKey('token', $response->json());
    }

    public function test_user_failed_to_login_if_email_not_exists(): void
    {
        $response = $this->postJson(route('auth.login'), [
            'email'    => 'wrong@example.com',
            'password' => $this->password
        ]);

        $response->assertUnauthorized();
    }

    public function test_user_failed_to_login_if_password_is_not_valid(): void
    {
        $response = $this->postJson(route('auth.login'), [
            'email'    => $this->email,
            'password' => 'wrong12312$#%$#'
        ]);

        $response->assertUnauthorized();
    }
}
