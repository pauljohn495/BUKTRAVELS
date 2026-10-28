<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DestinationResource;
use App\Models\Destination;
use App\Models\DestinationPhoto;
use App\Services\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class DestinationPhotoController extends Controller
{
    public function store(Request $request, Destination $destination): JsonResponse
    {
        Gate::authorize('update', $destination);
        $data = $request->validate([
            'photo' => ['required', 'file', 'mimetypes:image/jpeg,image/png,image/webp', 'max:8192'],
            'alt_text' => ['required', 'string', 'max:500'],
            'attribution' => ['nullable', 'string', 'max:500'],
            'source_url' => ['nullable', 'url:https', 'max:2000'],
            'license' => ['nullable', 'string', 'max:160'],
            'verification_status' => ['required', Rule::in(['unverified', 'verified'])],
        ]);
        if ($data['verification_status'] === 'verified') {
            Gate::authorize('verify', $destination);
        }

        $file = $request->file('photo');
        $path = $file->storePublicly('destinations/'.$destination->id, 'public');
        $photo = $destination->photos()->create([
            'url' => Storage::disk('public')->url($path),
            'disk' => 'public',
            'path' => $path,
            'original_name' => mb_substr($file->getClientOriginalName(), 0, 255),
            'alt_text' => $data['alt_text'],
            'attribution' => $data['attribution'] ?? null,
            'source_url' => $data['source_url'] ?? null,
            'license' => $data['license'] ?? null,
            'is_primary' => ! $destination->photos()->exists(),
            'verification_status' => $data['verification_status'],
            'sort_order' => ((int) $destination->photos()->max('sort_order')) + 1,
        ]);
        AuditLogger::record($request, 'photo.uploaded', $photo, null, $photo->toArray());

        return response()->json(['data' => (new DestinationResource($destination->fresh()->load(['municipality', 'category', 'photos', 'highlights', 'updates'])))->resolve($request)], 201);
    }

    public function reorder(Request $request, Destination $destination): JsonResponse
    {
        Gate::authorize('update', $destination);
        $data = $request->validate(['photo_ids' => ['required', 'array'], 'photo_ids.*' => ['integer', 'distinct']]);
        $photos = $destination->photos()->whereIn('id', $data['photo_ids'])->get()->keyBy('id');
        abort_unless($photos->count() === count($data['photo_ids']) && $photos->count() === $destination->photos()->count(), 422, 'Every destination photo must be included exactly once.');

        DB::transaction(function () use ($data, $photos) {
            foreach ($data['photo_ids'] as $index => $id) {
                $photos[$id]->update(['sort_order' => $index, 'is_primary' => $index === 0]);
            }
        });
        AuditLogger::record($request, 'photos.reordered', $destination, null, ['photo_ids' => $data['photo_ids']]);

        return response()->json(['data' => (new DestinationResource($destination->fresh()->load(['municipality', 'category', 'photos', 'highlights', 'updates'])))->resolve($request)]);
    }

    public function destroy(Request $request, Destination $destination, DestinationPhoto $photo): Response
    {
        Gate::authorize('update', $destination);
        abort_unless($photo->destination_id === $destination->id, 404);
        $before = $photo->toArray();
        if ($photo->disk && $photo->path) {
            Storage::disk($photo->disk)->delete($photo->path);
        }
        $photo->delete();
        $first = $destination->photos()->orderBy('sort_order')->first();
        $first?->update(['is_primary' => true]);
        AuditLogger::record($request, 'photo.deleted', $photo, $before, null);

        return response()->noContent();
    }
}
