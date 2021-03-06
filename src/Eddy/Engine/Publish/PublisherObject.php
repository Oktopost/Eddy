<?php
namespace Eddy\Engine\Publish;


use Eddy\Base\IEddyQueueObject;
use Eddy\Base\Engine\Lock\ILocker;
use Eddy\Base\Engine\Publish\IPublisherObject;
use Eddy\Enums\EventState;


/**
 * @autoload
 */
class PublisherObject implements IPublisherObject
{
	/**
	 * @autoload
	 * @var \Eddy\Base\Engine\Queue\IQueueBuilder
	 */
	private $builder;
	
	/**
	 * @autoload
	 * @var \Eddy\Base\Engine\IMainQueue
	 */
	private $main;
	
	/** 
	 * @context
	 * @var \Eddy\Base\IConfig 
	 */
	private $config;
	
	/** @var IEddyQueueObject */
	private $object;
	
	
	private function locker(): ILocker
	{
		$naming = $this->config->Naming;
		$queueName = $this->object->getQueueNaming($naming);
		
		return $this->config->Engine->Locker->get($queueName);
	}
	
	private function enqueue(array $data): void
	{
		$queue = $this->builder->getQueue($this->object);
		$queue->enqueue($data, $this->object->Delay);
	}
	
	private function startQueue()
	{
		$locker = $this->locker();
			
		if ($locker->isLocked())
			return;
		
		$naming = $this->config->Naming;
		$queueName = $this->object->getQueueNaming($naming);
		
		$this->main->schedule($queueName);
	}
	
	
	public function setObject(IEddyQueueObject $object): void
	{
		$this->object = $object;
	}

	public function publish(array $data): void
	{
		if (!in_array($this->object->State, EventState::ACTIVE_QUEUE_STATES))
			return;
		
		$this->enqueue($data);
		
		if ($this->object->State != EventState::PAUSED)
		{
			$this->startQueue();	
		}
	}
}