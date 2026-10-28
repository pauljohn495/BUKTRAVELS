<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Destination;
use App\Models\Municipality;
use App\Models\User;
use Database\Seeders\TourismContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DestinationApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_index_returns_the_eleven_migrated_unverified_destinations(): void
    {
        $this->seed(TourismContentSeeder::class);

        $response = $this->getJson('/api/destinations?per_page=48');

        $response->assertOk()->assertJsonCount(11, 'data');
        $this->assertSame(['unverified'], collect($response->json('data'))->pluck('verification_status')->unique()->values()->all());
        $this->assertNull($response->json('data.0.latitude'));
        $this->assertNull($response->json('data.0.longitude'));
    }

    public function test_public_index_filters_by_search_category_and_municipality(): void
    {
        $this->seed(TourismContentSeeder::class);

        $this->getJson('/api/destinations?search=lake')->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.slug', 'lakeapo');
        $this->getJson('/api/destinations?category=nature')->assertOk()->assertJsonCount(2, 'data');
        $this->getJson('/api/destinations?municipality=impasugong')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_drafts_are_not_public(): void
    {
        $this->seed(TourismContentSeeder::class);
        Destination::where('slug', 'dahilayan')->update(['status' => 'draft']);

        $this->getJson('/api/destinations/dahilayan')->assertNotFound();
        $this->getJson('/api/destinations?per_page=48')->assertOk()->assertJsonCount(10, 'data');
    }

    public function test_content_writes_require_an_authenticated_admin(): void
    {
        $this->seed(TourismContentSeeder::class);
        $payload = $this->validPayload();

        $this->postJson('/api/admin/destinations', $payload)->assertUnauthorized();

        Sanctum::actingAs(User::factory()->create(['role' => 'visitor']));
        $this->postJson('/api/admin/destinations', $payload)->assertForbidden();
    }

    public function test_admin_can_create_and_publish_a_destination(): void
    {
        $this->seed(TourismContentSeeder::class);
        Sanctum::actingAs(User::factory()->create(['role' => 'admin']), ['content:manage']);

        $this->postJson('/api/admin/destinations', $this->validPayload())
            ->assertCreated()
            ->assertJsonPath('data.slug', 'test-destination')
            ->assertJsonPath('data.status', 'published')
            ->assertJsonPath('data.verification_status', 'unverified');

        $this->assertDatabaseHas('destinations', ['slug' => 'test-destination', 'status' => 'published']);
    }

    private function validPayload(): array
    {
        $municipality = Municipality::first();
        $category = Category::first();

        return [
            'municipality_id' => $municipality->id,
            'category_id' => $category->id,
            'slug' => 'test-destination',
            'name' => 'Test Destination',
            'summary' => 'Unverified test content.',
            'description' => null,
            'address' => null,
            'latitude' => null,
            'longitude' => null,
            'access_notes' => null,
            'source_url' => null,
            'source_label' => null,
            'status' => 'published',
            'verification_status' => 'unverified',
            'verified_at' => null,
            'highlights' => ['Unverified highlight'],
        ];
    }
}
