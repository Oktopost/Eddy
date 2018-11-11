<?php
namespace Eddy\Engine\Queue\DeepQueue;


use DeepQueue\DeepQueue;
use Eddy\Base\Engine\Queue\IQueueManager;


class DeepQueueManagerAdapter implements IQueueManager
{
	private $name;
	
	/** @var DeepQueue */
	private $deepQueue;
	
	
	public function __construct(DeepQueue $deepQueue, string $name)
	{
		$this->deepQueue	= $deepQueue;
		$this->name			= $name;
	}

	
	public function clear()
	{
		$this->deepQueue->manager($this->name)->clearQueue();
	}

	/**
	 * @return float|null If que is empty, null returned. Otherwise number of seconds till
	 * next payload is ready to be processed. Zero if a payload is already should be dequeued.
	 * Should not return value less then zero.
	 */
	public function getNextRuntime(): ?float
	{
		$delayBuffer = 0;
		$packageSize = 0;	
		$queue = $this->deepQueue->getQueueObject($this->name);
		
		if ($queue)
		{
			$delayBuffer = $queue->Config->DelayBuffer;
			$packageSize = $queue->Config->PackageSize;
		}
		
		return $this->deepQueue->manager($this->name)->getWaitingTime($delayBuffer, $packageSize);
	}
}