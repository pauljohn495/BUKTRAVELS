<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AccountAuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(12)->letters()->numbers()],
        ]);

        $user = User::create($data + ['role' => 'visitor']);
        $token = $user->createToken('traveler-account', ['favorites:manage'])->plainTextToken;

        return response()->json($this->payload($user, $token), 201);
    }

    public function login(Request $request): JsonResponse
    {
        $data = $request->validate(['email' => ['required', 'email'], 'password' => ['required', 'string', 'max:255']]);
        $user = User::query()->where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages(['email' => ['The supplied credentials are invalid.']]);
        }

        $user->tokens()->where('name', 'traveler-account')->delete();
        $token = $user->createToken('traveler-account', ['favorites:manage'])->plainTextToken;

        return response()->json($this->payload($user, $token));
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json(['user' => $request->user()->only(['id', 'name', 'email', 'role'])]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json(['message' => 'Signed out.']);
    }

    private function payload(User $user, string $token): array
    {
        return ['token' => $token, 'user' => $user->only(['id', 'name', 'email', 'role'])];
    }
}
