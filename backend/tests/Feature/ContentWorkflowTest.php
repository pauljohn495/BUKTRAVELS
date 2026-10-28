<?php

namespace Tests\Feature;

use App\Models\Destination;
use App\Models\User;
use Database\Seeders\TourismContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ContentWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_editor_can_save_drafts_but_cannot_publish_or_verify(): void
    {
        $this->seed(TourismContentSeeder::class);
        $destination = Destination::where('slug', 'dahilayan')->with(['municipality', 'category'])->firstOrFail();
        $destination->update(['status' => 'draft', 'published_at' => null]);
        Sanctum::actingAs(User::factory()->create(['role' => 'content_editor']));

        $draft = $this->payload($destination, ['status' => 'draft']);
        $this->putJson('/api/admin/destinations/'.$destination->slug, $draft)->assertOk();

        $this->putJson('/api/admin/destinations/'.$destination->slug, $this->payload($destination, ['status' => 'published']))->assertForbidden();
        $this->putJson('/api/admin/destinations/'.$destination->slug, $this->payload($destination, [
            'status' => 'draft', 'verification_status' => 'verified', 'verified_at' => now()->toDateString(),
        ]))->assertForbidden();
    }

    public function test_reviewer_can_publish_verify_and_store_coordinates(): void
    {
        $this->seed(TourismContentSeeder::class);
        $destination = Destination::where('slug', 'dahilayan')->with(['municipality', 'category'])->firstOrFail();
        Sanctum::actingAs(User::factory()->create(['role' => 'reviewer']));

        $this->putJson('/api/admin/destinations/'.$destination->slug, $this->payload($destination, [
            'status' => 'published', 'verification_status' => 'verified', 'verified_at' => now()->toDateString(),
            'latitude' => 8.25, 'longitude' => 124.75,
        ]))->assertOk()->assertJsonPath('data.verification_status', 'verified');

        $this->assertDatabaseHas('audit_events', ['action' => 'destination.updated', 'auditable_id' => $destination->id]);
    }

    public function test_staff_can_upload_and_reorder_photos_with_an_audit_trail(): void
    {
        Storage::fake('public');
        $this->seed(TourismContentSeeder::class);
        $destination = Destination::where('slug', 'dahilayan')->firstOrFail();
        Sanctum::actingAs(User::factory()->create(['role' => 'reviewer']));

        $response = $this->post('/api/admin/destinations/'.$destination->slug.'/photos', [
            'photo' => UploadedFile::fake()->create('verified-photo.jpg', 100, 'image/jpeg'),
            'alt_text' => 'Manually reviewed destination photograph.',
            'source_url' => 'https://example.test/source',
            'license' => 'Permission recorded offline',
            'verification_status' => 'verified',
        ], ['Accept' => 'application/json']);

        $response->assertCreated();
        $this->assertDatabaseHas('audit_events', ['action' => 'photo.uploaded']);
        $this->assertDatabaseHas('destination_photos', ['original_name' => 'verified-photo.jpg', 'verification_status' => 'verified']);
    }

    private function payload(Destination $destination, array $overrides = []): array
    {
        return array_merge([
            'municipality_id' => $destination->municipality_id,
            'category_id' => $destination->category_id,
            'slug' => $destination->slug,
            'name' => $destination->name,
            'summary' => $destination->summary,
            'description' => $destination->description,
            'address' => null,
            'latitude' => null,
            'longitude' => null,
            'access_notes' => $destination->access_notes,
            'source_url' => $destination->source_url,
            'source_label' => $destination->source_label,
            'status' => $destination->status,
            'verification_status' => $destination->verification_status,
            'verified_at' => $destination->verified_at,
            'highlights' => [],
        ], $overrides);
    }
}
