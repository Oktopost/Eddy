<?php
namespace Eddy\Engine\Queue\DeepQueue;


use Eddy\Base\Engine\IQueue;


class DeepQueueDecorator implements IQueue
{
	/** @var \DeepQueue\Base\Queue\IQueue */
	private $queue;
	
	
	public function __construct(\DeepQueue\Base\Queue\IQueue $queue)
	{
		$this->queue = $queue;
	}


	public function enqueue(array $data, float $secDelay = 0): void
	{
		$this->queue->enqueueAll($data, $secDelay);
	}

	public function dequeue(int $maxCount): array
	{
		return $this->queue->dequeue($maxCount);
	}
}