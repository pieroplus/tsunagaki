<?php
declare(strict_types=1);
namespace App\Repositories\UserRepository;

use App\Dtos\Auth\Signup\SignupRequestDto;
use App\Models\User;

interface UserRepositoryInterface
{
    public function findOne(int $userId): ?User;
    public function signup(SignupRequestDto $dto): ?User;
}