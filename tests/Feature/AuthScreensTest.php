<?php

namespace Tests\Feature;

use Tests\TestCase;

class AuthScreensTest extends TestCase
{
    /**
     * Test login screen matches Figma MacBook Pro 14" - 27
     */
    public function test_login_screen_matches_figma_design(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);

        // Skyscraper panel & logo
        $response->assertSee('itpi_logo_tight.png');
        $response->assertSee('Welcome back!');
        $response->assertSee('Please enter your details to monitor your CR.');

        // Login form card
        $response->assertSee('Sign in with email');
        $response->assertSee('Email');
        $response->assertSee('Password');
        $response->assertSee('Forgot Password ?');
        $response->assertSee('SIGN IN');

        // Social and footer
        $response->assertSee('Our Continue With');
        $response->assertSee("Don't have an account?", false);
        $response->assertSee('Sign Up');
    }

    /**
     * Test register screen matches Figma MacBook Pro 14" - 28
     */
    public function test_register_screen_matches_figma_design(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);

        // Form elements
        $response->assertSee('Create an account');
        $response->assertSee('Nama');
        $response->assertSee('Email');
        $response->assertSee('Password');
        $response->assertSee('Get Started');
        $response->assertSee('Our Continue With');
        $response->assertSee('Already have an account?');
        $response->assertSee('Sign In');

        // Skyscraper panel & logo
        $response->assertSee('itpi_logo_tight.png');
        $response->assertSee('Welcome back!');
        $response->assertSee('Please enter your details to monitor your CR.');
    }

    /**
     * Test forgot password screen
     */
    public function test_forgot_password_screen_loads(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
        $response->assertSee('itpi_logo_tight.png');
        $response->assertSee('Forget Password ?');
    }
}
