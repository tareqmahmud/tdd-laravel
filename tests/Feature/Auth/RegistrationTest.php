<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use WithFaker;

    public function test_a_user_can_register_successfully(): void
    {
        $email = $this->faker()->email;
        $password = $this->faker()->password;

        $response = $this->postJson(route('auth.register'), [
            'name'                  => $this->faker()->name,
            'email'                 => $email,
            'password'              => $password,
            'password_confirmation' => $password,
        ]);

        $response->assertCreated();

        $this->assertDatabaseHas('users', [
            'email' => $email,
        ]);
    }

    public function test_user_registration_name_validation(): void
    {
        $this->withExceptionHandling();
        $email = $this->faker()->email;
        $password = $this->faker()->password;

        $response = $this->postJson(route('auth.register'), [
            'email'                 => $email,
            'password'              => $password,
            'password_confirmation' => $password,
        ]);

        $response->assertUnprocessable();

        $response->assertJsonValidationErrors('name');
    }

    public function test_user_registration_email_missing(): void
    {
        $this->withExceptionHandling();

        $password = $this->faker()->password;

        $response = $this->postJson(route('auth.register'), [
            'name'                  => $this->faker()->name,
            'password'              => $password,
            'password_confirmation' => $password,
        ]);

        $response->assertUnprocessable();

        $response->assertJsonValidationErrors('email');
    }

    public function test_user_registration_email_validation(): void
    {
        $this->withExceptionHandling();

        $password = $this->faker()->password;

        $response = $this->postJson(route('auth.register'), [
            'name'                  => $this->faker()->name,
            'email'                 => 'hello',
            'password'              => $password,
            'password_confirmation' => $password,
        ]);

        $response->assertUnprocessable();

        $response->assertJsonValidationErrors('email');
    }

    public function test_user_registration_password_min(): void
    {
        $this->withExceptionHandling();

        $email = $this->faker()->email;
        $password = $this->faker()->password(1, 4);
        $response = $this->postJson(route('auth.register'), [
            'name'                  => $this->faker()->name,
            'email'                 => $email,
            'password'              => $password,
            'password_confirmation' => $password,
        ]);

        $response->assertUnprocessable();

        $response->assertJsonValidationErrors('password');
    }

    public function test_user_registration_password_max(): void
    {
        $this->withExceptionHandling();

        $email = $this->faker()->email;
        $password = $this->faker()->password(35, 50);
        $response = $this->postJson(route('auth.register'), [
            'name'                  => $this->faker()->name,
            'email'                 => $email,
            'password'              => $password,
            'password_confirmation' => $password,
        ]);

        $response->assertUnprocessable();

        $response->assertJsonValidationErrors('password');
    }

    public function test_user_registration_password_confirmation_invalid(): void
    {
        $this->withExceptionHandling();

        $email = $this->faker()->email;
        $response = $this->postJson(route('auth.register'), [
            'name'                  => $this->faker()->name,
            'email'                 => $email,
            'password'              => $this->faker()->password(8, 30),
            'password_confirmation' => $this->faker()->password(8, 30),
        ]);

        $response->assertUnprocessable();

        $response->assertJsonValidationErrors('password');
    }

}
