<?php
namespace Eddy\Base\Engine\Publish;


use Eddy\Objects\EventObject;
use Eddy\Objects\HandlerObject;


/**
 * @skeleton
 */
interface IPublishBuilder
{
	public function getEventPublisher(EventObject $object): IPublisher;

	/**
	 * @param HandlerObject[] $handlers
	 * @return IPublisher
	 */
	public function getHandlersPublisher(array $handlers): IPublisher;
}