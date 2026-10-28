<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DestinationResource;
use App\Models\Destination;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DestinationController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'municipality' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:100'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:48'],
        ]);

        $destinations = Destination::query()
            ->where('status', 'published')
            ->with(['municipality', 'category', 'photos', 'highlights', 'updates'])
            ->when($validated['search'] ?? null, function ($query, $search) {
                $term = '%'.mb_strtolower($search).'%';
                $query->where(function ($query) use ($term) {
                    $query->whereRaw('LOWER(name) LIKE ?', [$term])
                        ->orWhereRaw('LOWER(COALESCE(summary, \'\')) LIKE ?', [$term])
                        ->orWhereRaw('LOWER(COALESCE(description, \'\')) LIKE ?', [$term])
                        ->orWhereHas('municipality', fn ($query) => $query->whereRaw('LOWER(name) LIKE ?', [$term]))
                        ->orWhereHas('category', fn ($query) => $query->whereRaw('LOWER(name) LIKE ?', [$term]));
                });
            })
            ->when($validated['municipality'] ?? null, fn ($query, $slug) => $query->whereHas('municipality', fn ($query) => $query->where('slug', $slug)))
            ->when($validated['category'] ?? null, fn ($query, $slug) => $query->whereHas('category', fn ($query) => $query->where('slug', $slug)))
            ->orderBy('name')
            ->paginate($validated['per_page'] ?? 24)
            ->withQueryString();

        return DestinationResource::collection($destinations);
    }

    public function show(Destination $destination): DestinationResource
    {
        abort_unless($destination->status === 'published', 404);

        return new DestinationResource($destination->load(['municipality', 'category', 'photos', 'highlights', 'updates']));
    }
}
