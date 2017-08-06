<?php
namespace Eddy\Base\Module;


use Eddy\Object\HandlerObject;


/**
 * @skeleton
 */
interface IHandlersModule
{
	public function pause(HandlerObject $object): void;
}