<?php
declare(strict_types=1);
namespace App\Repositories\UserRepository;

use App\Dtos\Auth\Signup\SignupRequestDto;
use App\Models\User;

class UserRepository implements UserRepositoryInterface
{
    /**
     * 指定のユーザを取得する
     * 
     * @param int $userId
     * @return User | null
     */
    public function findOne(int $userId): ?User
    {
        return User::findOrFail($userId);   
    }

    /**
     * ユーザを登録する
     * 
     * @param SignupRequestDto $dto
     * @return User | null
     */
    public function signup(SignupRequestDto $dto): ?User
    {
        $user = User::create([
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => $dto->getHashedPassword(),
        ]);
        return $user ?? null;
    }
}