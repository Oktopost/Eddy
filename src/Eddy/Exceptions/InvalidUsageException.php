<?php
namespace Eddy\Exceptions;


class InvalidUsageException extends EddyException
{
	public function __construct(string $message)
	{
		parent::__construct($message, 300);
	}
}