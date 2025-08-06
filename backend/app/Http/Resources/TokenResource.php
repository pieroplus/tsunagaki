<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TokenResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'token_type' => $this->tokenType,
            'access_token' => $this->accessToken,
            'refresh_token' => $this->refreshToken,
            'access_token_expires_at' => $this->accessTokenExpiresAt,
            'refresh_token_expires_at' => $this->refreshTokenExpiresAt,
            'user' => new UserResource($this->userDto),
        ];
    }
}
