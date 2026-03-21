<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\ApiResponse;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function me(): JsonResponse
    {
        return ApiResponse::success(new UserResource(auth('api')->user()));
    }

    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = auth('api')->user();
        $user->update($request->validated());

        return ApiResponse::success(new UserResource($user));
    }
}
