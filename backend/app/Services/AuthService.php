<?php
declare(strict_types=1);
namespace App\Services;

use App\Dtos\Commons\OperationMessageDto;
use App\Dtos\User\UserDto;
use App\Dtos\Auth\Signin\SigninRequestDto;
use App\Dtos\Auth\Signup\SignupRequestDto;
use App\Dtos\Auth\Token\RefreshTokenRequestDto;
use App\Dtos\Auth\Token\TokenResponseDto;
use App\Models\User;
use App\Repositories\RefreshTokenRepository\RefreshTokenRepositoryInterface;
use App\Repositories\UserRepository\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;

class AuthService
{

    public function __construct(
        private UserRepositoryInterface $userRepository,
         private RefreshTokenRepositoryInterface $refreshTokenRepository
    ){}

    /**
     * ユーザ登録
     * 
     * @param SignupRequestDto $dto
     * @return TokenResponseDto
     */
    public function signup(SignupRequestDto $dto): TokenResponseDto
    {
        $user = $this->userRepository->signup($dto);

        $accessToken = JWTAuth::fromUser($user);
        $accessTokenExpiresAt = now()->addMinutes(Auth::factory()->getTTL());
        $refreshTokenInfo = $this->createRefreshToken($user);
        return new TokenResponseDto(
            $accessToken,
            $accessTokenExpiresAt,
            $refreshTokenInfo['token'],
            $refreshTokenInfo['expires_at'],
            new UserDto(
                $user->id,
                $user->name,
                $user->email,
                $user->created_at,
            )
        );
    }

    /**
     * ログイン処理
     * 
     * @param SigninRequestDto $dto
     * @return TokenResponseDto
     */
    public function signin(SigninRequestDto $dto): TokenResponseDto
    {
        $signinField = $dto->signinField();
		$signinValue = $dto->signinValue();
        
        // 認証処理
        /** @var string|false $accessToken */
        $accessToken = Auth::attempt([$signinField => $signinValue, 'password' => $dto->password]);

		if (!$accessToken) {
			// throw new SigninUnauthorizedException();
		}
        $user = Auth::user();
        $accessTokenExpiresAt = now()->addMinutes(Auth::factory()->getTTL());
        $this->refreshTokenRepository->destoryByUser($user->id);
        $refreshTokenInfo = $this->createRefreshToken($user);
        return new TokenResponseDto(
            $accessToken,
            $accessTokenExpiresAt,
            $refreshTokenInfo['token'],
            $refreshTokenInfo['expires_at'],
            new UserDto(
                $user->id,
                $user->name,
                $user->email,
                $user->created_at,
            )
        );
    }

    /**
     * トークンの再発行
     * 
     * @param RefreshTokenRequestDto $dto
     * @return TokenResponseDto
     */
    public function refresh(RefreshTokenRequestDto $dto): TokenResponseDto
    {
        $hashedRefreshToken = $dto->getHashedRefreshToken();
        $refreshTokenRecord = $this->refreshTokenRepository->findOne($hashedRefreshToken);
        if (!$refreshTokenRecord) {
            $this->refreshTokenRepository->destoryByToken($hashedRefreshToken);
            // return response()->json(['error' => 'リフレッシュトークンの有効期限が切れています'], Response::HTTP_UNAUTHORIZED);
        }
        $user = $this->userRepository->findOne($refreshTokenRecord->user_id);

        $newAccessToken = JWTAuth::fromUser($user);
        $newAccessTokenExpiresAt = now()->addMinutes(Auth::factory()->getTTL());

        $this->refreshTokenRepository->destoryByToken($hashedRefreshToken);
        
        $refreshTokenInfo = $this->createRefreshToken($user);
        return new TokenResponseDto(
            $newAccessToken,
            $newAccessTokenExpiresAt,
            $refreshTokenInfo['token'],
            $refreshTokenInfo['expires_at'],
            new UserDto(
                $user->id,
                $user->name,
                $user->email,
                $user->created_at
            )
        );

    }

    /**
     * ログアウト
     * 
     * @param RefreshTokenRequestDto $dto
     * @return OperationMessageDto
     * 
     */
    public function signout(RefreshTokenRequestDto $dto): OperationMessageDto
    {
        $hashedRefreshToken = $dto->getHashedRefreshToken();
        Auth::logout();
        $this->refreshTokenRepository->destoryByToken($hashedRefreshToken);
        return new OperationMessageDto("ログアウトしました");

    }

    /**
     * リフレッシュトークンを生成する
     * 
     * @param User $user
     * @return Array
     */
    private function createRefreshToken(User $user): Array
    {
        $refreshToken = Str::random(60);
        $refreshTokenExpiresAt = now()->addDays(7);
        
        $this->refreshTokenRepository->create($user->id, hash('sha256', $refreshToken), $refreshTokenExpiresAt);
        return [
            'token' => $refreshToken,
            'expires_at' => $refreshTokenExpiresAt,
        ];
    }
}