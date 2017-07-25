<?php
namespace Eddy\Exceptions;


class EddyException extends \Exception
{
	public function __construct($message = "", $code = 0, \Throwable $previous = null)
	{
		parent::__construct("EddyException #$code: $message", $code, $previous);
	}
}