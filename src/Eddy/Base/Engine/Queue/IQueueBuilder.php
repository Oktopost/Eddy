<?php
namespace Eddy\Base\Engine\Queue;


use Eddy\Base\Engine\IQueue;
use Eddy\Base\Config\IConfigConsumer;


/**
 * @skeleton
 */
interface IQueueBuilder extends IConfigConsumer
{
	public function getQueue(string $name): IQueue;
}