<?php
namespace Eddy\Base\Engine\Publish;


use Eddy\Base\IEddyQueueObject;
use Eddy\Base\Config\IConfigConsumer;


/**
 * @skeleton
 */
interface IDefaultPublisher extends IPublisher, IConfigConsumer
{
	public function setEventObject(IEddyQueueObject $object): void;
}