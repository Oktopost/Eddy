<?php
namespace Eddy\Engine\Queue;


use Eddy\Base\Engine\IQueue;
use Eddy\Base\Config\IEngineConfig;
use Eddy\Base\Engine\Queue\IQueueDecorator;
use Eddy\Base\Engine\Queue\IQueueBuilder;
use Eddy\Base\IConfig;
use Eddy\Scope;


class QueueBuilder implements IQueueBuilder
{
	/** @var IEngineConfig */
	private $config;
	
	
	private function getDecorator($decorator, IQueue $child)
	{
		if ($decorator instanceof IQueueDecorator) $decorator = clone $decorator;
		else if (is_string($decorator)) $decorator = Scope::skeleton($decorator);
		
		$decorator->child($child);
	}
	
	
	public function setConfig(IConfig $config): void
	{
		$this->config = $config->Engine;
	}

	
	public function getQueue(string $name): IQueue
	{
		$queue = $this->config->QueueProvider->getQueue($name);
		$decorators = $this->config->QueueDecorators;
		
		foreach ($decorators as $decorator)
		{
			$queue = $this->getDecorator($decorator, $queue);
		}
		
		return $queue;
	}
}