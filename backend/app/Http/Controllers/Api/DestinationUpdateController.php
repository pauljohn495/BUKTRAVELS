<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Destination;
use App\Models\DestinationUpdate;
use App\Services\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class DestinationUpdateController extends Controller
{
    public function store(Request $request, Destination $destination): JsonResponse
    {
        Gate::authorize('update', $destination);
        $data = $this->validated($request);
        if ($data['status'] === 'published') {
            Gate::authorize('publish', $destination);
        }
        $update = $destination->updates()->create($data + [
            'created_by' => $request->user()->id,
            'published_at' => $data['status'] === 'published' ? now() : null,
        ]);
        AuditLogger::record($request, 'destination-update.created', $update, null, $update->toArray());

        return response()->json(['data' => $update], 201);
    }

    public function update(Request $request, DestinationUpdate $update): JsonResponse
    {
        Gate::authorize('update', $update->destination);
        $data = $this->validated($request);
        if ($data['status'] === 'published') {
            Gate::authorize('publish', $update->destination);
        }
        $before = $update->toArray();
        $update->update($data + ['published_at' => $data['status'] === 'published' ? ($update->published_at ?? now()) : null]);
        AuditLogger::record($request, 'destination-update.updated', $update, $before, $update->fresh()->toArray());

        return response()->json(['data' => $update->fresh()]);
    }

    public function destroy(Request $request, DestinationUpdate $update): Response
    {
        Gate::authorize('update', $update->destination);
        $before = $update->toArray();
        $update->delete();
        AuditLogger::record($request, 'destination-update.deleted', $update, $before, null);

        return response()->noContent();
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'body' => ['required', 'string', 'max:5000'],
            'status' => ['required', Rule::in(['draft', 'published'])],
        ]);
    }
}
