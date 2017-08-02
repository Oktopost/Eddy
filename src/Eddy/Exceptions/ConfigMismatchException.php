<?php
namespace Eddy\Exceptions;


class ConfigMismatchException extends EddyException
{
	public function __construct(string $message, int $code = -1)
	{
		parent::__construct($message, $code);
	}
}