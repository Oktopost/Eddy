<?php
namespace Eddy\Engine\Queue;


use Eddy\Base\Engine\IQueue;
use Eddy\Base\Engine\IMainQueue;
use Eddy\Base\Engine\Queue\IQueueManager;


/**
 * @context
 */
class MainQueue implements IMainQueue
{
	/**
	 * @context 
	 * @var \Eddy\Base\IConfig 
	 */
	private $config;
	
	/** @var IQueue */
	private $queue = null;
	
	/** @var IQueueManager */
	private $manager = null;
	
	
	private function getQueue(): IQueue
	{
		if (!$this->queue)
		{
			$mainQueueName = $this->config->Naming->MainQueueName;
			$queueProvider = $this->config->Engine->QueueProvider;
			
			$this->queue = $queueProvider->getQueue($mainQueueName);
			$this->manager = $queueProvider->getManager($mainQueueName);
		}
		
		return $this->queue;
	}
	
	
	public function schedule(string $target): void
	{
		$queue = $this->getQueue();
		$delay = $this->manager->getNextRuntime();
		
		if (is_null($delay)) return;
		
		$queue->enqueue([[$target => $target]], $delay);
	}
	
	public function dequeue(float $wait = 0): ?string
	{
		$queue = $this->getQueue();
		$result = $queue->dequeue(1, $wait);
		
		if (!$result)
			return null;
		
		$result = array_values($result);
		
		return $result[0];
	}
}