<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Municipality;
use Illuminate\Http\JsonResponse;

class TaxonomyController extends Controller
{
    public function categories(): JsonResponse
    {
        return response()->json(['data' => Category::query()->orderBy('name')->get(['id', 'name', 'slug'])]);
    }

    public function municipalities(): JsonResponse
    {
        return response()->json(['data' => Municipality::query()->orderBy('name')->get(['id', 'name', 'slug', 'type', 'verification_status'])]);
    }
}
