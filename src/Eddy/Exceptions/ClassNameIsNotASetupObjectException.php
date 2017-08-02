<?php

namespace Eddy\Exceptions;


class ClassNameIsNotASetupObjectException extends EddyException
{
	public function __construct($className = "", ?string $message)
	{
		$message = $message ?: 'No loader found for this class name';
		parent::__construct("Failed to parse class name: $className. $message", 200);
	}
}