<?php
declare(strict_types=1);
namespace App\Repositories\RefreshTokenRepository;
use App\Models\RefreshToken;
use Illuminate\Support\Carbon;

interface RefreshTokenRepositoryInterface
{
	public function findOne(string $hashedRefreshToken): ?RefreshToken;
	public function create(int $userId, string $hashedRefreshToken, Carbon $refreshTokenExpiresAt): void;
	public function destoryByToken(string $hashedRefreshToekn): void;
    public function destoryByUser(int $userId): void;
}