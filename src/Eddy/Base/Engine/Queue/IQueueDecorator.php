<?php
namespace Eddy\Base\Engine\Queue;


use Eddy\Base\Engine\IQueue;
use Eddy\Base\IEddyQueueObject;


interface IQueueDecorator extends IQueue
{
	public function child(IQueue $queue): void;
	public function setObject(IEddyQueueObject $object): void;
}