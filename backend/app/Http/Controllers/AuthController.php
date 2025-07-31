<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\RefreshToken;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\JsonResponse;
use \Symfony\Component\HttpFoundation\Response;


class AuthController extends Controller
{

    /**
     * ユーザー登録
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function signup(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $accessToken = JWTAuth::fromUser($user);
        $refresh = $this->createRefreshToken($user);

        return response()->json([
            'access_token' => $accessToken,
            'refresh_token' => $refresh['token'],
            'token_type' => 'bearer',
            'access_token_expires_at' => now()->addMinutes(Auth::factory()->getTTL())->toISOString(),
            'refresh_token_expires_at' => $refresh['expires_at'],
            'user' => $user,
        ], Response::HTTP_CREATED);
    }

    /**
     * ログイン処理（アクセストークンとリフレッシュトークンの発行）
     *
     * @param Request $request
     * @return JsonResponse
     * **/
    public function signin(Request $request): JsonResponse
    {
        $credentials = $request->only('email', 'name', 'password');

        $loginField = filter_var($credentials['email'] ?? $credentials['name'], FILTER_VALIDATE_EMAIL) ? 'email' : 'name';
        $loginValue = $credentials[$loginField];

        if (!$accessToken = Auth::attempt([$loginField => $loginValue, 'password' => $credentials['password']])) {
            return response()->json(['error' => 'Unauthorized'],  Response::HTTP_UNAUTHORIZED);
        }

        $user = Auth::user();
        RefreshToken::where('user_id', $user->id)->delete();

        $refresh = $this->createRefreshToken($user);

        return response()->json([
            'access_token' => $accessToken,
            'refresh_token' => $refresh['token'],
            'token_type' => 'bearer',
            'access_token_expires_at' => now()->addMinutes(Auth::factory()->getTTL())->toISOString(),
            'refresh_token_expires_at' => $refresh['expires_at'],
            'user' => $user,
        ], Response::HTTP_OK);
    }

    /**
     * アクセストークンの再発行（リフレッシュトークン使用）
     *
     * @param Request $request
     * @return JsonResponse
     **/
    public function refresh(Request $request): JsonResponse
    {
        $refreshToken = $request->input('refresh_token');
        $tokenHash = hash('sha256', $refreshToken);

        $refreshTokenRecord = RefreshToken::where('refresh_token', $tokenHash)
            ->where('refresh_token_expires_at', '>', now())
            ->first();

        if (!$refreshTokenRecord) {
            RefreshToken::where('refresh_token', $tokenHash)
                ->delete();
            return response()->json(['error' => 'リフレッシュトークンの有効期限が切れています'], Response::HTTP_UNAUTHORIZED);
        }

        $user = User::find($refreshTokenRecord->user_id);
        $newAccessToken = Auth::login($user);
        RefreshToken::where('refresh_token', $tokenHash)
            ->delete();
        $refresh = $this->createRefreshToken($user);

        return response()->json([
            'access_token' => $newAccessToken,
            'refresh_token' => $refresh['token'],
            'token_type' => 'bearer',
            'access_token_expires_at' => now()->addMinutes(Auth::factory()->getTTL())->toISOString(),
            'refresh_token_expires_at' => $refresh['expires_at'],
            'user' => $user,
        ], Response::HTTP_OK);
    }

    /**
     * ログアウト（アクセストークンの無効化 & リフレッシュトークンの削除）
     *
     * @param Request $request
     * @return JsonResponse
     **/
    public function logout(Request $request): JsonResponse
    {

        $refreshToken = $request->input('refresh_token');

        Auth::logout();
        RefreshToken::where('refresh_token', hash('sha256', $refreshToken))
            ->delete();

        return response()->json(['message' => 'ログアウトしました。'], Response::HTTP_OK);
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
        RefreshToken::create([
            'user_id' => $user->id,
            'refresh_token' => hash('sha256', $refreshToken),
            'refresh_token_expires_at' => now()->addDays(7),
        ]);
        return [
            'token' => $refreshToken,
            'expires_at' => $refreshTokenExpiresAt,
        ];
    }
}
