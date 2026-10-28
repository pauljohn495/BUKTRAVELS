<?php

namespace Tests\Feature;

use Database\Seeders\TourismContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TravelerAccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_traveler_can_register_and_merge_local_favorites(): void
    {
        $this->seed(TourismContentSeeder::class);

        $register = $this->postJson('/api/account/register', [
            'name' => 'Traveler',
            'email' => 'traveler@example.test',
            'password' => 'secure-password-123',
            'password_confirmation' => 'secure-password-123',
        ])->assertCreated()->assertJsonPath('user.role', 'visitor');

        $token = $register->json('token');
        $this->withToken($token)->postJson('/api/account/favorites/sync', ['slugs' => ['dahilayan', 'lakeapo']])
            ->assertOk()
            ->assertJsonCount(2, 'data');
        $this->withToken($token)->deleteJson('/api/account/favorites/dahilayan')
            ->assertOk()
            ->assertJsonPath('data.0', 'lakeapo');
    }

    public function test_favorite_endpoints_require_authentication(): void
    {
        $this->getJson('/api/account/favorites')->assertUnauthorized();
    }
}
