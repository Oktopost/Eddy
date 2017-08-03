<?php
namespace Eddy\Exceptions;


use Eddy\Object\HandlerObject;


class HandlerMismatchConfiguration extends ConfigMismatchException
{
	public function __construct($handlerClassName, HandlerObject $config)
	{
		parent::__construct(
			"The configuration defined for the handler '$handlerClassName' points " . 
				"to a different handler class '{$config->HandlerClassName}'. " . 
				"In config: '{$config->ConfigClassName}'", 320);
	}
}