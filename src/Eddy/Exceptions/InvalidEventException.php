<?php
namespace Eddy\Exceptions;


class InvalidEventException extends EddyException
{
	public function __construct($name)
	{
		parent::__construct("Event '$name' does not exist", 100);
	}
}