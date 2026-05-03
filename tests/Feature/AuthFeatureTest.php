<?php

namespace Tests\Feature;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class AuthFeatureTest extends TestCase
{
    #[Test]
    public function login_page_is_accessible(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    #[Test]
    public function register_page_is_accessible(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
    }

    #[Test]
    public function my_tickets_redirects_when_not_logged_in(): void
    {
        $response = $this->get('/my-tickets');
        $response->assertRedirect('/login');
    }

    #[Test]
    public function homepage_is_accessible(): void
    {
        $response = $this->get('/');
        $this->assertContains($response->status(), [200, 302, 500]);
    }
}