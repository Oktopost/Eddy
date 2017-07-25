<?php
namespace Eddy\Base\Engine\Queue;


use Eddy\Base\Engine\IQueue;

interface IQueueDecorator extends IQueue
{
	public function child(IQueue $queue): void;
}