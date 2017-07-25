<?php
namespace Eddy\Exceptions;


class UnexpectedException extends EddyException
{
	public function __construct($message)
	{
		parent::__construct($message, -1);
	}
}