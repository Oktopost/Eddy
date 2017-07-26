<?php
namespace Eddy\Exceptions;


class ConfigMismatchException extends EddyException
{
	public function __construct(string $message)
	{
		parent::__construct($message);
	}
}