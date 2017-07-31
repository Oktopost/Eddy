<?php
namespace Eddy\Base\Engine\Queue;


use Eddy\Base\IEddyQueueObject;
use Eddy\Base\Engine\IQueue;


/**
 * @skeleton
 */
interface IQueueBuilder
{
	public function getQueue(IEddyQueueObject $object): IQueue;
}