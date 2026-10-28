<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Destination;
use App\Models\Municipality;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TourismContentSeeder extends Seeder
{
    public function run(): void
    {
        $municipalities = [
            ['name' => 'Manolo Fortich', 'type' => 'municipality'],
            ['name' => 'Impasugong', 'type' => 'municipality'],
            ['name' => 'Valencia City', 'type' => 'city'],
            ['name' => 'Malaybalay City', 'type' => 'city'],
            ['name' => 'Bukidnon Highlands', 'type' => 'region_placeholder'],
            ['name' => 'Quezon', 'type' => 'municipality'],
        ];
        foreach ($municipalities as $item) {
            Municipality::updateOrCreate(
                ['slug' => Str::slug($item['name'])],
                $item + ['verification_status' => 'unverified']
            );
        }

        foreach (['Adventure', 'Scenic', 'Lakes', 'Culture', 'Nature', 'Hiking', 'Countryside'] as $name) {
            Category::updateOrCreate(['slug' => Str::slug($name)], ['name' => $name, 'description' => null]);
        }

        $images = [
            'hills' => 'https://images.unsplash.com/photo-1500534623283-312aade485b7?w=900&auto=format&fit=crop&q=82',
            'forest' => 'https://images.unsplash.com/photo-1448375240586-882707db888b?w=900&auto=format&fit=crop&q=82',
            'lake' => 'https://images.unsplash.com/photo-1501785888041-af3ef285b470?w=900&auto=format&fit=crop&q=82',
            'mountain' => 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?w=900&auto=format&fit=crop&q=82',
            'green' => 'https://images.unsplash.com/photo-1441974231531-c6227db76b6e?w=900&auto=format&fit=crop&q=82',
            'sunrise' => 'https://images.unsplash.com/photo-1470770841072-f978cf4d019e?w=900&auto=format&fit=crop&q=82',
        ];

        $places = [
            ['slug' => 'dahilayan', 'name' => 'Dahilayan Adventure Park', 'town' => 'Manolo Fortich', 'category' => 'Adventure', 'summary' => 'Mountain ziplining and thrilling outdoor rides among cool highland scenery.', 'description' => 'A well-known highland adventure destination in Barangay Dahilayan, with zipline attractions and outdoor experiences.', 'highlights' => ['Zipline', 'Mountain setting', 'Adventure rides'], 'access_notes' => 'Check the official park website for current ride operations, availability and fees.', 'source_url' => 'https://www.dahilayanadventurepark.com/', 'source_label' => 'Official park', 'image' => $images['forest']],
            ['slug' => 'communal', 'name' => 'Impasugong Communal Ranch', 'town' => 'Impasugong', 'category' => 'Scenic', 'summary' => 'Open grasslands, rolling hills and cinematic countryside views.', 'description' => 'A large scenic ranch in Impasugong, popular for its expansive grasslands, scenic hills and photography.', 'highlights' => ['Rolling hills', 'Countryside views', 'Photography'], 'access_notes' => 'Road access can be rough. Confirm registration and current access rules with the local tourism office.', 'source_url' => 'https://bukidnon.gov.ph/impasugong/', 'source_label' => 'Bukidnon Provincial Government', 'image' => $images['hills']],
            ['slug' => 'lakeapo', 'name' => 'Lake Apo', 'town' => 'Valencia City', 'category' => 'Lakes', 'summary' => 'A tranquil crater lake framed by green highland slopes.', 'description' => 'A scenic crater lake in Valencia City, known for its calm views and lakeside recreation, including floating cottages when available.', 'highlights' => ['Crater lake', 'Relaxing views', 'Floating cottages'], 'access_notes' => 'Confirm local access, water activities, and rental arrangements before visiting.', 'source_url' => 'https://philippines.travel/destinations/bukidnon/index', 'source_label' => 'Philippines Travel', 'image' => $images['lake']],
            ['slug' => 'monastery', 'name' => 'Monastery of the Transfiguration', 'town' => 'Malaybalay City', 'category' => 'Culture', 'summary' => 'A peaceful monastery celebrated for its distinctive architecture.', 'description' => 'A Benedictine religious complex and cultural landmark in Malaybalay City, known for its quiet atmosphere and memorable church architecture.', 'highlights' => ['Benedictine heritage', 'Architecture', 'Quiet reflection'], 'access_notes' => 'This is an active religious place. Dress modestly, observe silence and verify visitor access.', 'source_url' => 'https://sakata.malaybalaycity.gov.ph/tourism/', 'source_label' => 'Malaybalay Tourism', 'image' => $images['green']],
            ['slug' => 'cedar', 'name' => 'CEDAR Eco Park', 'town' => 'Impasugong', 'category' => 'Nature', 'summary' => 'Refreshing forest walks, green trails and waterfall scenery.', 'description' => 'The Center for Ecological Development and Recreation is a forest recreation destination in Impasugong known for trails and natural scenery.', 'highlights' => ['Forest walks', 'Green scenery', 'Waterfalls'], 'access_notes' => 'Wear suitable shoes and check trail conditions, guides and access requirements.', 'source_url' => 'https://bukidnon.gov.ph/impasugong/', 'source_label' => 'Bukidnon Provincial Government', 'image' => $images['forest']],
            ['slug' => 'panimahawa', 'name' => 'Panimahawa Ridge', 'town' => 'Impasugong', 'category' => 'Hiking', 'summary' => 'Rolling ridge walks and sunrise views when the weather is right.', 'description' => 'A popular hike in Impasugong, with rolling highland grasslands and expansive viewpoints that can reveal a sea of clouds.', 'highlights' => ['Sunrise hikes', 'Ridgelines', 'Open landscapes'], 'access_notes' => 'Check the weather and ask about guide and trail access rules before hiking.', 'source_url' => 'https://bukidnon.gov.ph/impasugong/', 'source_label' => 'Bukidnon Provincial Government', 'image' => $images['sunrise']],
            ['slug' => 'kaamulan', 'name' => 'Kaamulan Park', 'town' => 'Malaybalay City', 'category' => 'Culture', 'summary' => 'A green cultural landmark connected to Bukidnon traditions.', 'description' => "A park and gathering space in Malaybalay associated with the province's Kaamulan celebrations and indigenous cultural heritage.", 'highlights' => ['Cultural grounds', 'Park strolls', 'Local heritage'], 'access_notes' => 'Respect Indigenous cultural traditions and verify dates for any special events.', 'source_url' => 'https://bukidnon.gov.ph/kaamulan-festival/', 'source_label' => 'Provincial Government', 'image' => $images['green']],
            ['slug' => 'pineapple', 'name' => 'Del Monte Pineapple Fields', 'town' => 'Manolo Fortich', 'category' => 'Countryside', 'summary' => 'Discover the agricultural landscapes that define Bukidnon.', 'description' => "The cultivated pineapple countryside around Manolo Fortich offers a glimpse of the province's agricultural character and wide open landscapes.", 'highlights' => ['Farming landscapes', 'Scenic drives', 'Photography'], 'access_notes' => 'Fields are working private property. Observe from public areas unless a visit is officially authorized.', 'source_url' => 'https://philippines.travel/destinations/bukidnon/index', 'source_label' => 'Philippines Travel', 'image' => $images['hills']],
            ['slug' => 'kitanglad', 'name' => 'Mt. Kitanglad Range Natural Park', 'town' => 'Bukidnon Highlands', 'category' => 'Hiking', 'summary' => 'A magnificent protected mountain landscape rich in biodiversity.', 'description' => 'A protected natural park encompassing mountains and forests in Bukidnon, with ecological and cultural importance.', 'highlights' => ['Protected forests', 'Highland trekking', 'Biodiversity'], 'access_notes' => 'Trekking requires advance coordination, appropriate permits and qualified local guidance.', 'source_url' => 'https://www.aseanbiodiversity.org/asean-heritage-parks/mt-kitanglad-range-naturai-park/', 'source_label' => 'ASEAN Centre for Biodiversity', 'image' => $images['mountain']],
            ['slug' => 'nasuli', 'name' => 'Nasuli Spring', 'town' => 'Malaybalay City', 'category' => 'Nature', 'summary' => 'Cool spring waters and lush surroundings for a refreshing escape.', 'description' => 'A natural spring destination near Malaybalay where visitors come for greenery, fresh water and relaxation.', 'highlights' => ['Natural spring', 'Nature break', 'Green surroundings'], 'access_notes' => 'Check current swimming rules, water safety and local opening arrangements.', 'source_url' => 'https://sakata.malaybalaycity.gov.ph/tourism/', 'source_label' => 'Malaybalay Tourism', 'image' => $images['lake']],
            ['slug' => 'overview', 'name' => 'Overview Park', 'town' => 'Quezon', 'category' => 'Scenic', 'summary' => 'A roadside viewpoint with dramatic hills and broad highland vistas.', 'description' => 'A scenic roadside stop along the Bukidnon-Davao highway in Quezon, offering a highland viewpoint on the journey.', 'highlights' => ['Viewpoint', 'Road-trip stop', 'Mountain scenery'], 'access_notes' => 'Stay clear of roadway traffic and confirm weather visibility before stopping.', 'source_url' => 'https://bukidnon.gov.ph/about-bukidnon/', 'source_label' => 'Provincial Government', 'image' => $images['mountain']],
        ];

        foreach ($places as $place) {
            $destination = Destination::updateOrCreate(
                ['slug' => $place['slug']],
                [
                    'name' => $place['name'],
                    'municipality_id' => Municipality::where('name', $place['town'])->value('id'),
                    'category_id' => Category::where('name', $place['category'])->value('id'),
                    'summary' => $place['summary'],
                    'description' => $place['description'],
                    'address' => null,
                    'latitude' => null,
                    'longitude' => null,
                    'access_notes' => $place['access_notes'],
                    'source_url' => $place['source_url'],
                    'source_label' => $place['source_label'],
                    'status' => 'published',
                    'verification_status' => 'unverified',
                    'verified_at' => null,
                    'published_at' => now(),
                ]
            );

            $destination->highlights()->delete();
            foreach ($place['highlights'] as $index => $highlight) {
                $destination->highlights()->create(['label' => $highlight, 'verification_status' => 'unverified', 'sort_order' => $index]);
            }

            $destination->photos()->delete();
            $destination->photos()->create([
                'url' => $place['image'],
                'alt_text' => 'Illustrative scenic placeholder; not a verified photo of '.$place['name'].'.',
                'attribution' => null,
                'source_url' => null,
                'license' => null,
                'is_primary' => true,
                'verification_status' => 'unverified',
                'sort_order' => 0,
            ]);
        }
    }
}
