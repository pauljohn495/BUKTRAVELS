<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Destination;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json(['data' => $request->user()->favorites()->where('status', 'published')->pluck('slug')->values()]);
    }

    public function store(Request $request, Destination $destination): JsonResponse
    {
        abort_unless($destination->status === 'published', 404);
        $request->user()->favorites()->syncWithoutDetaching([$destination->id]);

        return response()->json(['data' => $request->user()->favorites()->where('status', 'published')->pluck('slug')->values()]);
    }

    public function destroy(Request $request, Destination $destination): JsonResponse
    {
        $request->user()->favorites()->detach($destination->id);

        return response()->json(['data' => $request->user()->favorites()->where('status', 'published')->pluck('slug')->values()]);
    }

    public function sync(Request $request): JsonResponse
    {
        $data = $request->validate(['slugs' => ['required', 'array', 'max:100'], 'slugs.*' => ['string', 'max:160', 'distinct']]);
        $ids = Destination::query()->where('status', 'published')->whereIn('slug', $data['slugs'])->pluck('id');
        $request->user()->favorites()->syncWithoutDetaching($ids);

        return response()->json(['data' => $request->user()->favorites()->where('status', 'published')->pluck('slug')->values()]);
    }
}
