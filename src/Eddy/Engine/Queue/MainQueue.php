<?php
namespace Eddy\Engine\Queue;


use Eddy\Base\Engine\Queue\IQueueManager;
use Eddy\Base\IConfig;
use Eddy\Base\IEddyQueueObject;
use Eddy\Base\Engine\IQueue;
use Eddy\Base\Engine\IMainQueue;


class MainQueue implements IMainQueue
{
	/** @var IConfig */
	private $config;
	
	/** @var IQueue */
	private $queue = null;
	
	/** @var IQueueManager */
	private $manager = null;
	
	
	public function setConfig(IConfig $config): void
	{
		$this->config = $config;
	}

	public function schedule(IEddyQueueObject $object): void
	{
		if (!$this->queue)
		{
			$mainQueueName = $this->config->Naming->MainQueueName;
			$queueProvider = $this->config->Engine->QueueProvider;
			
			$this->queue = $queueProvider->getQueue($mainQueueName);
			$this->manager = $queueProvider->getManager($mainQueueName);
		}
		
		$delay = $this->manager->getNextRuntime();
		
		if (is_null($delay)) return;
		
		$name = $object->getQueueNaming($this->config->Naming);
		$this->queue->enqueue([$name => $name], $delay);
	}
}