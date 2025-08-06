<?php
declare(strict_types=1);
namespace App\Dtos\Auth\Signin;

class SigninRequestDto
{
    public readonly ?string $email;
    public readonly ?string $name;
    public readonly string $password;

    public function __construct(?string $email, ?string $name, string $password)
    {
		$this->email = $email;
		$this->name = $name;
		$this->password = $password;
    }

    public function signinField(): string
    {
        return filter_var($this->email ?? $this->name, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';
    }

    public function signinValue(): string
    {
        return $this->email ?? $this->name;
    }

}