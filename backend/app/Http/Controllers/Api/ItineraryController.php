<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Destination;
use App\Models\Itinerary;
use App\Models\ItineraryStop;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ItineraryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $itineraries = $request->user()->itineraries()->latest()->get();

        return response()->json(['data' => $itineraries->map(fn (Itinerary $itinerary) => $this->serialize($itinerary))]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validateItinerary($request);
        $itinerary = $request->user()->itineraries()->create($data);

        return response()->json(['data' => $this->serialize($itinerary)], 201);
    }

    public function update(Request $request, Itinerary $itinerary): JsonResponse
    {
        Gate::authorize('update', $itinerary);
        $itinerary->update($this->validateItinerary($request));

        return response()->json(['data' => $this->serialize($itinerary)]);
    }

    public function destroy(Request $request, Itinerary $itinerary): Response
    {
        Gate::authorize('delete', $itinerary);
        $itinerary->delete();

        return response()->noContent();
    }

    public function addStop(Request $request, Itinerary $itinerary, Destination $destination): JsonResponse
    {
        Gate::authorize('update', $itinerary);
        abort_unless($destination->status === 'published', 404);
        abort_if($itinerary->stops()->where('destination_id', $destination->id)->exists(), 422, 'This destination is already in the itinerary.');
        $data = $this->validateStop($request);
        $itinerary->stops()->create([
            ...$data,
            'destination_id' => $destination->id,
            'position' => ((int) $itinerary->stops()->max('position')) + 1,
        ]);

        return response()->json(['data' => $this->serialize($itinerary)], 201);
    }

    public function updateStop(Request $request, Itinerary $itinerary, ItineraryStop $stop): JsonResponse
    {
        Gate::authorize('update', $itinerary);
        abort_unless($stop->itinerary_id === $itinerary->id, 404);
        $stop->update($this->validateStop($request));

        return response()->json(['data' => $this->serialize($itinerary)]);
    }

    public function reorder(Request $request, Itinerary $itinerary): JsonResponse
    {
        Gate::authorize('update', $itinerary);
        $data = $request->validate(['stop_ids' => ['required', 'array'], 'stop_ids.*' => ['integer', 'distinct']]);
        $stops = $itinerary->stops()->whereIn('id', $data['stop_ids'])->get()->keyBy('id');
        abort_unless($stops->count() === count($data['stop_ids']) && $stops->count() === $itinerary->stops()->count(), 422, 'Every itinerary stop must be included exactly once.');
        DB::transaction(function () use ($data, $stops) {
            foreach ($data['stop_ids'] as $position => $id) {
                $stops[$id]->update(['position' => $position + 1]);
            }
        });

        return response()->json(['data' => $this->serialize($itinerary)]);
    }

    public function removeStop(Request $request, Itinerary $itinerary, ItineraryStop $stop): JsonResponse
    {
        Gate::authorize('update', $itinerary);
        abort_unless($stop->itinerary_id === $itinerary->id, 404);
        $stop->delete();

        return response()->json(['data' => $this->serialize($itinerary)]);
    }

    private function validateItinerary(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'start_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);
    }

    private function validateStop(Request $request): array
    {
        return $request->validate([
            'visit_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);
    }

    private function serialize(Itinerary $itinerary): array
    {
        $itinerary->load(['stops.destination.municipality', 'stops.destination.category']);

        return [
            'id' => $itinerary->id,
            'name' => $itinerary->name,
            'start_date' => $itinerary->start_date?->toDateString(),
            'notes' => $itinerary->notes,
            'stops' => $itinerary->stops->map(fn (ItineraryStop $stop) => [
                'id' => $stop->id,
                'position' => $stop->position,
                'visit_date' => $stop->visit_date?->toDateString(),
                'notes' => $stop->notes,
                'destination' => [
                    'id' => $stop->destination->id,
                    'slug' => $stop->destination->slug,
                    'name' => $stop->destination->name,
                    'latitude' => $stop->destination->latitude,
                    'longitude' => $stop->destination->longitude,
                    'verification_status' => $stop->destination->verification_status,
                    'municipality' => $stop->destination->municipality->name,
                    'category' => $stop->destination->category->name,
                ],
            ])->values(),
            'created_at' => $itinerary->created_at,
            'updated_at' => $itinerary->updated_at,
        ];
    }
}
