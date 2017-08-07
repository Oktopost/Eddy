<?php
namespace Eddy\Engine\Queue;


use Eddy\Base\Engine\IQueue;
use Eddy\Base\Engine\IMainQueue;
use Eddy\Base\Engine\Queue\IQueueManager;
use Eddy\Exceptions\AbortException;


/**
 * @context
 */
class MainQueue implements IMainQueue
{
	const ABORT_INDICATOR	= 'Eddy:Command:00000000';
	
	
	/**
	 * @context 
	 * @var \Eddy\Base\IConfig 
	 */
	private $config;
	
	/** @var IQueue */
	private $queue = null;
	
	
	private function getQueue(): IQueue
	{
		if (!$this->queue)
		{
			$mainQueueName = $this->config->Naming->MainQueueName;
			$queueProvider = $this->config->Engine->QueueProvider;
			
			$this->queue = $queueProvider->getQueue($mainQueueName);
		}
		
		return $this->queue;
	}
	
	
	public function sendAbort(int $count = 100): void
	{
		$queue = $this->getQueue();
		
		$data = array_fill(0, $count, self::ABORT_INDICATOR);
		$queue->enqueue($data);
	}
	
	public function schedule(string $target): void
	{
		$queue = $this->getQueue();
		
		$manager = $this->config->Engine->QueueProvider->getManager($target);
		$delay = $manager->getNextRuntime();
		
		if (is_null($delay)) return;
		
		$queue->enqueue([$target => $target], $delay);
	}
	
	public function dequeue(float $wait = 0): ?string
	{
		$queue = $this->getQueue();
		$result = $queue->dequeue(1, $wait);
		
		if (!$result)
			return null;
		
		$result = array_values($result);
		$result = $result[0];
		
		if ($result == self::ABORT_INDICATOR)
			throw new AbortException();
		
		return $result;
	}
}