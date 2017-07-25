<?php
namespace Eddy\Engine\Queue;


use Eddy\Base\Engine\IQueue;
use Eddy\Base\Engine\IQueueProvider;

use DeepQueue\DeepQueue;


class DeepQueueProvider implements IQueueProvider
{
	public function __construct(DeepQueue $queue)
	{
	}
	

	public function getQueue(): IQueue
	{
		// TODO: Implement getQueue() method.
	}
}