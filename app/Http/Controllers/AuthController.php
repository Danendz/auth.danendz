<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\ApiResponse;
use App\Http\Resources\AuthResponseResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private readonly AuthService $auth)
    {
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

        $result = $this->auth->register(
            $data['name'],
            $data['email'],
            $data['password'],
            $data['device_name'] ?? 'api',
        );

        return ApiResponse::success(new AuthResponseResource($result));
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $data = $request->validated();

        $result = $this->auth->login(
            $data['email'],
            $data['password'],
            $data['device_name'] ?? 'api',
        );

        return ApiResponse::success(new AuthResponseResource($result));
    }

    public function logout(): JsonResponse
    {
        $this->auth->logout();

        return ApiResponse::success(null, 'Logged out');
    }

    public function refresh(): JsonResponse
    {
        return ApiResponse::success(new AuthResponseResource($this->auth->refresh()));
    }
}
