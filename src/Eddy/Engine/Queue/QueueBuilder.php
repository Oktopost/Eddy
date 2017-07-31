<?php
namespace Eddy\Engine\Queue;


use Eddy\Base\IConfig;
use Eddy\Base\IEddyQueueObject;
use Eddy\Base\Engine\IQueue;
use Eddy\Base\Engine\Queue\IQueueBuilder;
use Eddy\Base\Engine\Queue\IQueueDecorator;

use Eddy\Exceptions\UnexpectedException;

use Eddy\Scope;


class QueueBuilder implements IQueueBuilder
{
	/** @context IConfig */
	private $config;
	
	
	private function getDecorator($decorator): IQueueDecorator
	{
		if ($decorator instanceof IQueueDecorator)
		{
			return clone $decorator;
		}
		else if (is_string($decorator))
		{
			return Scope::skeleton($decorator);
		}
		
		throw new UnexpectedException('Expecting IQueueDecorator instance or class name.');
	}
	
	
	public function getQueue(IEddyQueueObject $object): IQueue
	{
		$name = $object->getQueueNaming($this->config->Naming);
		$engineConfig = $this->config->Engine;
		
		$queue = $engineConfig->QueueProvider->getQueue($name);
		$decorators = $engineConfig->QueueDecorators;
		
		foreach ($decorators as $decorator)
		{
			$item = $this->getDecorator($decorator);
			$item->child($queue);
			$item->setObject($object);
			
			$queue = $item;
		}
		
		return $queue;
	}
}