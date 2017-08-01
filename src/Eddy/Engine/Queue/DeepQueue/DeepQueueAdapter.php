<?php
namespace Eddy\Engine\Queue\DeepQueue;


use DeepQueue\DeepQueue;
use Eddy\Base\Engine\IQueue;


class DeepQueueAdapter implements IQueue
{
	private $name;
	
	/** @var DeepQueue */
	private $deepQueue;
	
	/** @var \DeepQueue\Base\Queue\IQueue */
	private $queue;
	
	
	public function __construct(DeepQueue $deepQueue, string $name)
	{
		$this->deepQueue	= $deepQueue;
		$this->queue		= $deepQueue->get($name);
		$this->name			= $name;
	}


	public function enqueue(array $data, float $secDelay = 0): void
	{
		if (!$data)	return;
		
		$this->queue->enqueueAll($data, $secDelay);
	}

	public function dequeue(int $maxCount): array
	{
		return $this->queue->dequeue($maxCount);
	}
}