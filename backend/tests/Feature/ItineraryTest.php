<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\TourismContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItineraryTest extends TestCase
{
    use RefreshDatabase;

    public function test_traveler_can_create_edit_and_reorder_an_itinerary(): void
    {
        $this->seed(TourismContentSeeder::class);
        $user = User::factory()->create(['role' => 'visitor']);
        $token = $user->createToken('test')->plainTextToken;

        $itinerary = $this->withToken($token)->postJson('/api/account/itineraries', [
            'name' => 'Weekend plan',
            'start_date' => '2026-11-07',
            'notes' => 'Confirm access before leaving.',
        ])->assertCreated()->assertJsonPath('data.name', 'Weekend plan')->json('data');

        $first = $this->withToken($token)->postJson("/api/account/itineraries/{$itinerary['id']}/destinations/dahilayan", [
            'visit_date' => '2026-11-07',
            'notes' => 'Morning option.',
        ])->assertCreated()->assertJsonCount(1, 'data.stops')->json('data.stops.0');
        $second = $this->withToken($token)->postJson("/api/account/itineraries/{$itinerary['id']}/destinations/lakeapo")
            ->assertCreated()->assertJsonCount(2, 'data.stops')->json('data.stops.1');

        $this->withToken($token)->putJson("/api/account/itineraries/{$itinerary['id']}/stops/reorder", [
            'stop_ids' => [$second['id'], $first['id']],
        ])->assertOk()->assertJsonPath('data.stops.0.destination.slug', 'lakeapo');

        $this->withToken($token)->putJson("/api/account/itineraries/{$itinerary['id']}/stops/{$second['id']}", [
            'visit_date' => '2026-11-08',
            'notes' => 'Second day.',
        ])->assertOk()->assertJsonPath('data.stops.0.notes', 'Second day.');
    }

    public function test_itineraries_are_private_to_their_owner(): void
    {
        $owner = User::factory()->create(['role' => 'visitor']);
        $other = User::factory()->create(['role' => 'visitor']);
        $itinerary = $owner->itineraries()->create(['name' => 'Private plan']);
        $token = $other->createToken('test')->plainTextToken;

        $this->withToken($token)->putJson("/api/account/itineraries/{$itinerary->id}", [
            'name' => 'Changed',
        ])->assertForbidden();
        $this->withToken($token)->deleteJson("/api/account/itineraries/{$itinerary->id}")->assertForbidden();
    }
}
