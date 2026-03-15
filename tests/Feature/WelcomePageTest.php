<?php

namespace Tests\Feature;

use Tests\TestCase;

class WelcomePageTest extends TestCase
{
    public function test_welcome_page_exposes_admin_sign_in_link(): void
    {
        $response = $this->get('/');

        $response
            ->assertOk()
            ->assertSee('Admin Sign In')
            ->assertSee('/login')
            ->assertSee('admin@skt.co.tz');
    }
}