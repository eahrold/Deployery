<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BaseTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testRedirectToLogin()
    {
        $response = $this->get('/');

        $response->assertStatus(302);
    }

    public function testDoNotRedirectToLogin()
    {
        $user = factory(\App\Models\User::class)->create();
        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
    }
}
