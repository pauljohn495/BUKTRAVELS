<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuditEventController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $data = $request->validate(['action' => ['nullable', 'string', 'max:100']]);
        $events = AuditEvent::with('actor:id,name,email')->when($data['action'] ?? null, fn ($query, $action) => $query->where('action', $action))->latest()->paginate(50);

        return response()->json($events);
    }
}
