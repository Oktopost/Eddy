<?php
namespace Eddy\Engine\Publish;


use Eddy\Base\Config\IConfigConsumer;
use Eddy\Base\Engine\Publish\IPublisher;
use Eddy\Base\IConfig;
use Eddy\Enums\EventState;
use Eddy\Object\EventObject;


class DefaultPublisher implements IPublisher, IConfigConsumer
{
	/** @var IConfig */
	private $config;
	
	/** @var EventObject */
	private $object;
	
	public function setConfig(IConfig $config): void
	{
		$this->config = $config;
	}
	
	public function setEventObject(EventObject $object): void
	{
		$this->object = $object;
		
	}

	public function publish(array $data): void
	{
		$this->object = $this->config->DAL()->events()->load($this->object->Id);
		
		if ($this->object->State == EventState::STOPPED)
			return;
		
		
		// Enqueue
		
		if ($this->object->State != EventState::PAUSED)
		{
			// Get locker
			// If not locked, start.
		}
	}
}