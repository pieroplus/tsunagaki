<?php
declare(strict_types=1);
namespace App\Dtos\Auth\Signup;

use Illuminate\Support\Facades\Hash;

class SignupRequestDto
{

	public readonly string $email;
	public readonly string $name;
	public readonly string $password;

	public function __construct(
		string $email,
		string $name,
		string $password,
	)
	{
		$this->email = $email;
		$this->name = $name;
		$this->password = $password;
	}

    /**
     * ハッシュ化したパスワードを取得
     * 
     * @return string
     */
    public function getHashedPassword(): string
    {
        return Hash::make($this->password);
    }
}