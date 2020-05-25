<?php
namespace Eddy\Base\Module;


use Eddy\Objects\EventObject;
use Eddy\Objects\HandlerObject;


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