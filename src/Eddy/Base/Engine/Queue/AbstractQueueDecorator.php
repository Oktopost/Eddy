<?php
namespace Eddy\Base\Engine\Queue;


use Eddy\Base\Engine\IQueue;
use Eddy\Base\IEddyQueueObject;


abstract class AbstractQueueDecorator implements IQueueDecorator
{
	/** @var IQueue */
	private $childQueue = null;
	
	/** @var IEddyQueueObject */
	private $object = null;
	
	
	protected function getQueue(): IQueue
	{
		return $this->childQueue;
	}
	
	protected function getObject(): IEddyQueueObject
	{
		return $this->object;
	}

	
	public function child(IQueue $queue): void
	{
		$this->childQueue = $queue;
	}

	public function setObject(IEddyQueueObject $object): void
	{
		$this->object = $object;
	}

	public abstract function enqueue(array $data, float $secDelay = 0.0): void;
	public abstract function dequeue(int $maxCount): array;
}