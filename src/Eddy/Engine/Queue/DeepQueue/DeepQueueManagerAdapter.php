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
		throw new \Exception('TODO');
	}

	/**
	 * @return float|null If que is empty, null returned. Otherwise number of seconds till
	 * next payload is ready to be processed. Zero if a payload is already should be dequeued.
	 * Should not return value less then zero.
	 */
	public function getNextRuntime(): ?float
	{
		throw new \Exception('TODO');
	}
}