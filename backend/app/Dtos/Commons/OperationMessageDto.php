<?php
declare(strict_types=1);
namespace App\Dtos\Commons;

class OperationMessageDto
{
	public readonly string $message;

	public function __construct($message)
	{
		$this->message = $message;
	}
}