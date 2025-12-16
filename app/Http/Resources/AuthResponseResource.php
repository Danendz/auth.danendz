<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthResponseResource extends JsonResource
{
	public function toArray(Request $request): array
	{
		return [
            'token' => $this['access_token'],
            'token_type' => 'bearer',
            'expires_in' => $this['expires_in']
		];
	}
}
