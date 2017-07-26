<?php
namespace Eddy\Base\Engine\Publish;


use Eddy\Base\Config\IConfigConsumer;

use Eddy\Object\EventObject;
use Eddy\Object\HandlerObject;


/**
 * @skeleton
 */
interface IPublishBuilder extends IConfigConsumer
{
	public function getEventPublisher(EventObject $object): IPublisher;

	/**
	 * @param HandlerObject[] $handlers
	 * @return IPublisher
	 */
	public function getHandlersPublisher(array $handlers): IPublisher;
}