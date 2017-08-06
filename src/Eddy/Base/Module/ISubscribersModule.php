<?php
namespace Eddy\Base\Module;


use Eddy\Object\EventObject;
use Eddy\Object\HandlerObject;


/**
 * @skeleton
 */
interface ISubscribersModule
{
	/**
	 * @param EventObject $eventObject
	 * @return HandlerObject[]
	 */
	public function get(EventObject $eventObject): array;
}