<?php
declare(strict_types=1);

namespace App\Dtos\Auth\Token;

use App\Dtos\User\UserDto;
use Illuminate\Support\Carbon;

class TokenResponseDto
{
	public readonly string $tokenType;
	public readonly string $accessToken;
	public readonly Carbon $accessTokenExpiresAt;
	public readonly string $refreshToken;
	public readonly Carbon $refreshTokenExpiresAt;
	public readonly UserDto $userDto;  
    
    public function __construct(
        string $accessToken,
        Carbon $accessTokenExpiresAt,
        string $refreshToken,
        Carbon $refreshTokenExpiresAt,
        UserDto $userDto,
    )
    {
		$this->tokenType = 'bearer';
		$this->accessToken = $accessToken;
		$this->accessTokenExpiresAt = $accessTokenExpiresAt;
		$this->refreshToken = $refreshToken;
		$this->refreshTokenExpiresAt = $refreshTokenExpiresAt;
		$this->userDto = $userDto;  
    }
}