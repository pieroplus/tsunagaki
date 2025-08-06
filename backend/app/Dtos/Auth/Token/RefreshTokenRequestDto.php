<?php
declare(strict_types=1);

namespace App\Dtos\Auth\Token;

class RefreshTokenRequestDto
{
    public readonly string $refreshToken;

    public function __construct(string $refreshToken)
    {
        $this->refreshToken = $refreshToken;
    }

    public function getHashedRefreshToken(): string
    {
        return hash('sha256', $this->refreshToken);
    }
}