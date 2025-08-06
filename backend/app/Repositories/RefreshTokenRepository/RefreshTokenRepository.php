<?php
declare(strict_types=1);
namespace App\Repositories\RefreshTokenRepository;

use App\Models\RefreshToken;
use Illuminate\Support\Carbon;

class RefreshTokenRepository implements RefreshTokenRepositoryInterface
{

    public function findOne(string $hashedRefreshToken): ?RefreshToken
    {

        $refreshToken = RefreshToken::where('refresh_token', $hashedRefreshToken)
            ->where('refresh_token_expires_at', '>', now())
            ->first();
        return $refreshToken ?? null;
    }

	public function create(int $userId, string $hashedRefreshToken, Carbon $refreshTokenExpiresAt): void
    {

        RefreshToken::create([
            'user_id' => $userId,
            'refresh_token' => $hashedRefreshToken,
            'refresh_token_expires_at' => $refreshTokenExpiresAt,
        ]);

    }

    public function destoryByToken(string $hashedRefreshToken): void
    {
        RefreshToken::where('refresh_token', $hashedRefreshToken)->delete();
    }

    public function destoryByUser(int $userId): void
    {
        RefreshToken::where('user_id', $userId)->delete();

    }



}