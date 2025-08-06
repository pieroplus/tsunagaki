<?php
namespace App\Dtos\User;

use Illuminate\Support\Carbon;

class UserDto
{
	public readonly string $id;
	public $name;
	public $email;
	public $createdAt;
	
    public function __construct(
		int $id,
		string $name,
		string $email,
		Carbon $createdAt,
    ) {
		$this->id = $id;
		$this->name = $name;
		$this->email = $email;
		$this->createdAt = $createdAt;
	}

}
