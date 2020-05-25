<?php
namespace Eddy\Engine\Processor\ByTypeProcessors;


use Eddy\Base\Engine\Processor\ProcessTarget;
use Eddy\Base\Engine\Processor\IPayloadProcessor;

use Eddy\IHandlerConfig;
use Eddy\Objects\EventObject;
use Eddy\Objects\HandlerObject;


/**
 * @autoload
 */
class EventPayload implements IPayloadProcessor
{
	/**
	 * @autoload
	 * @var \Eddy\Base\Module\ISubscribersModule
	 */
	private $subscribers;
	
	/**
	 * @autoload
	 * @var \Eddy\Base\Engine\IMainQueue
	 */
	private $mainQueue;

	/**
	 * @context
	 * @var \Eddy\Base\IConfig
	 */
	private $config;

	/**
	 * @autoload
	 * @var \Eddy\Base\Engine\Queue\IQueueBuilder
	 */
	private $builder;
	
	
	private function enqueueOne(HandlerObject $object, array $payload)
	{
		if (!$object->isActive())
			return;
		
		$queue = $this->builder->getQueue($object);
		$queue->enqueue($payload, $object->Delay);
	}
	
	private function sanitize(HandlerObject $handlerObject, array $payload): array
	{
		if (!$handlerObject->isActive())
			return $payload;
		
		$handlerConfig = $handlerObject->getConfigInstance();
		
		$processed = [];
		
		foreach ($payload as $key => $item)
		{
			if (!$handlerConfig->filter($item))
				continue;
			
			$item = $handlerConfig->convert($item);
			$processed[$key] = $item;
		}
		
		return $processed;
	}
	
	
	public function process(ProcessTarget $target): void
	{
		/** @var EventObject $event */
		$event = $target->Object;
		
		$subscribers = $this->subscribers->get($event);
		
		foreach ($subscribers as $subscriber)
		{
			$payload = $this->sanitize($subscriber, $target->Payload);
			
			if (!$payload)
				continue;
			
			$this->enqueueOne($subscriber, $payload);
			$this->mainQueue->schedule($subscriber->getQueueNaming($this->config->Naming));
		}
	}
}