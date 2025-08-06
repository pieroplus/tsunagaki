<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Dtos\Auth\Signin\SigninRequestDto;
use App\Dtos\Auth\Signup\SignupRequestDto;
use App\Dtos\Auth\Token\RefreshTokenRequestDto;
use App\Http\Requests\Auth\SigninRequest;
use App\Http\Requests\Auth\SignupRequest;
use App\Http\Requests\Auth\RefreshTokenRequest;
use App\Http\Resources\MessageResource;
use App\Http\Resources\TokenResource;
use Illuminate\Http\Request;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;



class AuthController extends Controller
{

    public function __construct(private AuthService $authService){}

    /**
     * ユーザー登録
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function signup(SignupRequest $request): JsonResponse
    {

        $params = $request->safe()->toArray();
        $signupRequestDto = new SignupRequestDto($params['email'], $params['name'], $params['password']);
        $tokenResponseDto = $this->authService->signup($signupRequestDto);

        return (new TokenResource($tokenResponseDto))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * ログイン処理（アクセストークンとリフレッシュトークンの発行）
     *
     * @param Request $request
     * @return TokenResource
     * **/
    public function signin(SigninRequest $request): TokenResource
    {
        $params = $request->safe()->array();
        $signinRequestDto = new SigninRequestDto($params['email'] ?? null, $params['name'] ?? null, $params['password']);
        $tokenResponseDto = $this->authService->signin($signinRequestDto);

        return new TokenResource($tokenResponseDto);
    }

    /**
     * アクセストークンの再発行（リフレッシュトークン使用）
     *
     * @param Request $request
     * @return TokenResource
     **/
    public function refresh(RefreshTokenRequest $request): TokenResource
    {
        $params = $request->safe()->array();
        $refreshTokenRequestDto = new RefreshTokenRequestDto($params['refresh_token']);
        $tokenResponseDto = $this->authService->refresh($refreshTokenRequestDto);

        return new TokenResource($tokenResponseDto);
    }

    /**
     * ログアウト（アクセストークンの無効化 & リフレッシュトークンの削除）
     *
     * @param Request $request
     * @return MessageResource
     **/
    public function signout(RefreshTokenRequest $request): MessageResource

    {
        $params = $request->safe()->array();
        $refreshTokenRequestDto = new RefreshTokenRequestDto($params['refresh_token']);
        $operationMessageDto = $this->authService->signout($refreshTokenRequestDto);
        return new MessageResource($operationMessageDto);
    }
}
