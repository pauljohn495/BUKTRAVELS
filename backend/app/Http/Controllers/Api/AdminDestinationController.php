<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DestinationRequest;
use App\Http\Resources\DestinationResource;
use App\Models\Destination;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class AdminDestinationController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', Destination::class);

        return DestinationResource::collection(
            Destination::with(['municipality', 'category', 'photos', 'highlights', 'updates'])->orderBy('name')->paginate(50)
        );
    }

    public function store(DestinationRequest $request): DestinationResource
    {
        Gate::authorize('create', Destination::class);
        $this->authorizeReviewFields($request, new Destination);
        $destination = DB::transaction(fn () => $this->persist(new Destination, $request->validated(), $request->user()->id));
        AuditLogger::record($request, 'destination.created', $destination, null, $destination->toArray());

        return new DestinationResource($destination->load(['municipality', 'category', 'photos', 'highlights', 'updates']));
    }

    public function update(DestinationRequest $request, Destination $destination): DestinationResource
    {
        Gate::authorize('update', $destination);
        $this->authorizeReviewFields($request, $destination);
        $before = $destination->toArray();
        $destination = DB::transaction(fn () => $this->persist($destination, $request->validated(), $request->user()->id));
        AuditLogger::record($request, 'destination.updated', $destination, $before, $destination->fresh()->toArray());

        return new DestinationResource($destination->load(['municipality', 'category', 'photos', 'highlights', 'updates']));
    }

    public function destroy(Request $request, Destination $destination): Response
    {
        Gate::authorize('delete', $destination);
        $before = $destination->toArray();
        $destination->delete();
        AuditLogger::record($request, 'destination.deleted', $destination, $before, null);

        return response()->noContent();
    }

    private function persist(Destination $destination, array $data, int $userId): Destination
    {
        $hasHighlights = array_key_exists('highlights', $data);
        $hasPhotos = array_key_exists('photos', $data);
        $highlights = Arr::pull($data, 'highlights', []);
        $photos = Arr::pull($data, 'photos', []);
        $wasPublished = $destination->status === 'published';

        $destination->fill($data);
        $destination->created_by ??= $userId;
        $destination->updated_by = $userId;
        $destination->published_at = $data['status'] === 'published'
            ? ($wasPublished ? $destination->published_at : now())
            : null;
        if ($data['verification_status'] === 'unverified') {
            $destination->verified_at = null;
        }
        $destination->save();

        if ($hasHighlights) {
            $destination->highlights()->delete();
            foreach ($highlights as $index => $label) {
                $destination->highlights()->create(['label' => $label, 'verification_status' => 'unverified', 'sort_order' => $index]);
            }
        }

        if ($hasPhotos) {
            $destination->photos()->delete();
            foreach ($photos as $index => $photo) {
                $destination->photos()->create($photo + [
                    'is_primary' => $index === 0,
                    'verification_status' => $photo['verification_status'] ?? 'unverified',
                    'sort_order' => $index,
                ]);
            }
        }

        return $destination;
    }

    private function authorizeReviewFields(DestinationRequest $request, Destination $destination): void
    {
        $data = $request->validated();
        if ($data['status'] === 'published' && $destination->status !== 'published') {
            Gate::authorize('publish', $destination);
        }
        $coordinatesChanged = (string) ($data['latitude'] ?? '') !== (string) ($destination->latitude ?? '')
            || (string) ($data['longitude'] ?? '') !== (string) ($destination->longitude ?? '');
        if (($data['verification_status'] === 'verified' && $destination->verification_status !== 'verified') || $coordinatesChanged) {
            Gate::authorize('verify', $destination);
        }
    }
}
