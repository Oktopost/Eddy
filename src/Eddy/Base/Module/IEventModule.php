<?php
namespace Eddy\Base\Module;


use Eddy\Object\EventObject;


/**
 * @skeleton
 */
interface IEventModule
{
	public function loadByInterfaceName(string $interfaceName): ?EventObject;
}