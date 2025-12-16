<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function register(string $name, string $email, string $password, string $deviceName = 'api'): array
    {
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        return $this->create_token($user['email'], $user['password']);
    }

    public function login(string $email, string $password, string $deviceName = 'api'): array
    {
        return $this->create_token($email, $password);
    }

    public function logout(): void
    {
        auth('api')->logout();
    }

    public function refresh(): array
    {
        return [
            'access_token' => auth('api')->refresh(),
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ];
    }

    private function create_token(string $email, string $password): array
    {
        if (!$token = auth('api')->attempt([
            'email' => $email,
            'password' => $password,
        ])) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return ['access_token' => $token, 'expires_in' => auth('api')->factory()->getTTL() * 3600];
    }
}
