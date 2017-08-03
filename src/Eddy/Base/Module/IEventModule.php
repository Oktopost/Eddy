<?php
namespace Eddy\Base\Module;


use Eddy\IEventConfig;
use Eddy\Object\EventObject;
use Eddy\Object\HandlerObject;


/**
 * @skeleton
 */
interface IEventModule
{
	public function loadByInterfaceName(string $interfaceName): ?EventObject;
}