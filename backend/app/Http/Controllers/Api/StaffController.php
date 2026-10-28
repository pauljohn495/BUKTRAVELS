<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StaffController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['data' => User::whereIn('role', ['content_editor', 'reviewer', 'admin'])->orderBy('name')->get(['id', 'name', 'email', 'role', 'created_at'])]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in(['content_editor', 'reviewer', 'admin'])],
            'password' => ['required', 'confirmed', Password::min(12)->letters()->numbers()],
        ]);
        $user = User::create($data);
        AuditLogger::record($request, 'staff.created', $user, null, $user->only(['id', 'name', 'email', 'role']));

        return response()->json(['data' => $user->only(['id', 'name', 'email', 'role'])], 201);
    }

    public function update(Request $request, User $staff): JsonResponse
    {
        abort_unless($staff->isStaff(), 404);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($staff->id)],
            'role' => ['required', Rule::in(['content_editor', 'reviewer', 'admin'])],
            'password' => ['nullable', 'confirmed', Password::min(12)->letters()->numbers()],
        ]);
        if ($staff->role === 'admin' && $data['role'] !== 'admin' && User::where('role', 'admin')->count() <= 1) {
            abort(422, 'The last administrator cannot be demoted.');
        }
        $before = $staff->only(['id', 'name', 'email', 'role']);
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }
        $staff->update($data);
        AuditLogger::record($request, 'staff.updated', $staff, $before, $staff->only(['id', 'name', 'email', 'role']));

        return response()->json(['data' => $staff->only(['id', 'name', 'email', 'role'])]);
    }
}
