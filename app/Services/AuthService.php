<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AuthService
{
    public function register(string $name, string $email, string $password, string $deviceName = 'api'): array
    {
        User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        return $this->create_token($email, $password);
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
            'expires_in' => $this->get_expires_in(),
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

        return ['access_token' => $token, 'expires_in' => $this->get_expires_in()];
    }

    private function get_expires_in() {
        return auth('api')->factory()->getTTL() * 60;
    }
}
