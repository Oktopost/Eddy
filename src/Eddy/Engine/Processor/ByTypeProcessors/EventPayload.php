<?php
namespace Eddy\Engine\Processor\ByTypeProcessors;


use Eddy\Base\Engine\Processor\ProcessTarget;
use Eddy\Base\Engine\Processor\IPayloadProcessor;
use Eddy\Object\EventObject;
use Eddy\Object\HandlerObject;


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
	 * @context
	 * @var \Eddy\Base\IConfig
	 */
	private $config;
	
	
	private function enqueueOne(HandlerObject $object, array $payload)
	{
		if (!$object->isActive())
			return;
		
		$queueName = $object->getQueueNaming($this->config->Naming);
		$queue = $this->config->Engine->QueueProvider->getQueue($queueName);
		
		$queue->enqueue($payload, $object->Delay);
	}
	
	
	public function process(ProcessTarget $target): void
	{
		/** @var EventObject $event */
		$event = $target->Object;
		
		$subscribers = $this->subscribers->get($event);
		
		foreach ($subscribers as $subscriber)
		{
			$this->enqueueOne($subscriber, $target->Payload);
		}
	}
}