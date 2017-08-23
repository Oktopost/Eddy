<?php
namespace Eddy\Modules;


use Eddy\Base\IEddyQueueObject;
use Eddy\Base\Module\IEddyObjectModule;


/**
 * @context
 */
class EddyObjectModule implements IEddyObjectModule
{
	/**
	 * @context
	 * @var \Eddy\Base\IConfig
	 */
	private $config;
	
	
	private function isEvent(string $queueName): bool
	{
		return strpos($queueName, $this->config->Naming->EventQueuePrefix) !== false;
	}
	
	private function isHandler(string $queueName): bool
	{
		return strpos($queueName, $this->config->Naming->HandlerQueuePrefix) !== false;
	}
	
	private function getObjectName(string $queueName, $prefix): string
	{
		return str_replace($prefix, '', $queueName);
	}
	
	
	public function getByQueueName(string $queueName): ?IEddyQueueObject
	{
		$object = null;
		
		if ($this->isEvent($queueName))
		{
			$name = $this->getObjectName($queueName, $this->config->Naming->EventQueuePrefix);
			
			$object = $this->config->DAL()->events()->loadByName($name);
		}
		
		if ($this->isHandler($queueName))
		{
			$name = $this->getObjectName($queueName, $this->config->Naming->HandlerQueuePrefix);
			
			$object = $this->config->DAL()->handlers()->loadByName($name);
		}
		
		return $object;
	}

	public function getAllRunning(): array
	{
		$events = $this->config->DAL()->events()->loadAllRunning();
		$handlers = $this->config->DAL()->handlers()->loadAllRunning();
		
		return array_merge($events, $handlers);
	}
}