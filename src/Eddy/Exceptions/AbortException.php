<?php
namespace Eddy\Exceptions;


class AbortException extends EddyException
{
	public function __construct()
	{
		parent::__construct('Abort processor', -1);
	}
}