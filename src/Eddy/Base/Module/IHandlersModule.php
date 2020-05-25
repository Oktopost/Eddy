<?php
namespace Eddy\Base\Module;


use Eddy\Objects\HandlerObject;


/**
 * @skeleton
 */
interface IHandlersModule
{
	public function pause(HandlerObject $object): void;
}