<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_staff_users_can_receive_a_content_token(): void
    {
        User::factory()->create(['email' => 'visitor@example.test', 'password' => Hash::make('correct-password'), 'role' => 'visitor']);
        User::factory()->create(['email' => 'admin@example.test', 'password' => Hash::make('correct-password'), 'role' => 'admin']);
        User::factory()->create(['email' => 'editor@example.test', 'password' => Hash::make('correct-password'), 'role' => 'content_editor']);

        $this->postJson('/api/admin/login', ['email' => 'visitor@example.test', 'password' => 'correct-password'])
            ->assertUnprocessable();

        $this->postJson('/api/admin/login', ['email' => 'admin@example.test', 'password' => 'correct-password'])
            ->assertOk()
            ->assertJsonPath('user.role', 'admin')
            ->assertJsonStructure(['token', 'user' => ['id', 'name', 'email', 'role']]);

        $this->postJson('/api/admin/login', ['email' => 'editor@example.test', 'password' => 'correct-password'])
            ->assertOk()
            ->assertJsonPath('user.role', 'content_editor');
    }
}
