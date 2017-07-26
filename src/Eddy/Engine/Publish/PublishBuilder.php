<?php
namespace Eddy\Engine\Publish;


use Eddy\Base\IEddyQueueObject;
use Eddy\Object\EventObject;
use Eddy\Object\HandlerObject;

use Eddy\Base\IConfig;
use Eddy\Base\Engine\Publish\IPublisher;
use Eddy\Base\Engine\Publish\IPublishBuilder;
use Eddy\Base\Engine\Publish\IPublisherObject;

use Eddy\Scope;


class PublishBuilder implements IPublishBuilder
{
	/** @var IConfig */
	private $config;
	
	
	private function getPublisherFor(IEddyQueueObject $object): IPublisher
	{
		/** @var IPublisherObject $publisher */
		$publisher = Scope::skeleton(IPublisherObject::class);
		
		$publisher->setConfig($this->config);
		$publisher->setObject($object);
		
		return $publisher;
	}
	

	public function setConfig(IConfig $config): void
	{
		$this->config = $config;
	}

	public function getEventPublisher(EventObject $object): IPublisher
	{
		return $this->getPublisherFor($object);
	}

	/**
	 * @param HandlerObject[] $handlers
	 * @return IPublisher
	 */
	public function getHandlersPublisher(array $handlers): IPublisher
	{
		$collection = new PublishersCollection();
		
		foreach ($handlers as $object)
		{
			$collection->add($this->getPublisherFor($object));
		}
		
		return $collection;
	}
}