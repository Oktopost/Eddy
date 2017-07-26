<?php
namespace Eddy\Engine\Publish;


use Eddy\Base\IConfig;
use Eddy\Base\Engine\Lock\ILocker;
use Eddy\Base\Engine\Publish\IDefaultPublisher;
use Eddy\Enums\EventState;
use Eddy\Object\EventObject;

use Eddy\Scope;


/**
 * @autoload
 */
class DefaultPublisher implements IDefaultPublisher
{
	private $queueName;
	
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
	
	/** @var IConfig */
	private $config;
	
	/** @var EventObject */
	private $object;
	
	
	private function refreshObject(): void
	{
		$this->object = $this->config->DAL()->events()->load($this->object->Id);
		$this->queueName = $this->config->Naming->EventQueuePrefix . $this->object->Name;
	}
	
	private function locker(string $name): ILocker
	{
		return $this->config->Engine->Locker->get($name);
	}
	
	private function enqueue(array $data): void
	{
		$queue = $this->builder->getQueue($this->queueName);
		$queue->enqueue($data, $this->object->Delay);
	}
	
	private function startQueue()
	{
		$locker = $this->locker($this->queueName);
			
		if ($locker->isLocked())
			return;
		
		$this->main->schedule($this->queueName);
	}
	
	
	public function setConfig(IConfig $config): void
	{
		$this->config = $config;
		
		$this->main->setConfig($config);
		$this->builder->setConfig($config);
	}
	
	public function setEventObject(EventObject $object): void
	{
		$this->object = $object;
	}

	public function publish(array $data): void
	{
		$this->refreshObject();
		
		if ($this->object->State == EventState::STOPPED)
			return;
		
		$this->enqueue($data);
		
		if ($this->object->State != EventState::PAUSED)
		{
			$this->startQueue();	
		}
	}
}