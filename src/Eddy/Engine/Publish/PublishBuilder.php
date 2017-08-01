<?php
namespace Eddy\Engine\Publish;


use Eddy\Base\IEddyQueueObject;
use Eddy\Object\EventObject;
use Eddy\Object\HandlerObject;

use Eddy\Base\Engine\Publish\IPublisher;
use Eddy\Base\Engine\Publish\IPublishBuilder;
use Eddy\Base\Engine\Publish\IPublisherObject;

use Eddy\Scope;


/**
 * @context
 */
class PublishBuilder implements IPublishBuilder
{
	private function getPublisherFor(IEddyQueueObject $object): IPublisher
	{
		/** @var IPublisherObject $publisher */
		$publisher = Scope::skeleton($this, IPublisherObject::class);
		$publisher->setObject($object);
		return $publisher;
	}
	
	
	public function getEventPublisher(EventObject $object): IPublisher
	{
		$publisher = $this->getPublisherFor($object);
		
		$prepare = new PreparePayloadPublisher($publisher);
		$prepare->setEventConfig($object->getConfig());
		
		return $prepare;
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