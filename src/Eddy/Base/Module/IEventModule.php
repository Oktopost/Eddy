<?php
namespace Eddy\Base\Module;


use Eddy\Objects\EventObject;


/**
 * @skeleton
 */
interface IEventModule
{
	public function loadByInterfaceName(string $interfaceName): EventObject;
}