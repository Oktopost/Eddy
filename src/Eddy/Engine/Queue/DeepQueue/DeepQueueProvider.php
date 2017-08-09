<?php
namespace Eddy\Engine\Queue\DeepQueue;


use Eddy\Base\Engine\IQueue;
use Eddy\Base\Engine\Queue\IQueueManager;
use Eddy\Base\Engine\Queue\IQueueObjectManager;
use Eddy\Base\Engine\Queue\IQueueProvider;

use DeepQueue\DeepQueue;


class DeepQueueProvider implements IQueueProvider
{
	/** @var DeepQueue */
	private $deepQueue;
	
	
	public function __construct(DeepQueue $queue)
	{
		$this->deepQueue = $queue;
	}
	
	
	public function getQueue(string $name): IQueue
	{
		return new DeepQueueAdapter($this->deepQueue, $name);
	}

	public function getManager(string $name): IQueueManager
	{
		return new DeepQueueManagerAdapter($this->deepQueue, $name);
	}
	
	public function getObjectManager(): IQueueObjectManager
	{
		return new DeepQueueObjectManagerAdapter($this->deepQueue);
	}
}