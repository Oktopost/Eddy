<?php
namespace Eddy\Base\Engine\Publish;


use Eddy\Base\IEddyQueueObject;
use Eddy\Base\Config\IConfigConsumer;


/**
 * @skeleton
 */
interface IPublisherObject extends IPublisher
{
	public function setObject(IEddyQueueObject $object): void;
}