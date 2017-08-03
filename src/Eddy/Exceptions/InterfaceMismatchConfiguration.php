<?php
namespace Eddy\Exceptions;


use Eddy\Object\EventObject;


class InterfaceMismatchConfiguration extends ConfigMismatchException
{
	public function __construct($interface, EventObject $config)
	{
		parent::__construct(
			"The configuration defined for the interface '$interface' points " . 
				"to a different interface '{$config->EventInterface}'. In config: '{$config->ConfigClassName}'", 310);
	}
}