<?php
namespace SanityTestNS;


use Eddy\Event\DynamicEventConfig;


/**
 * @event ASanityEvent
 */
interface ASanityEventInterface
{
	
}

class ASanityConfig extends DynamicEventConfig
{
	public function eventClassName(): string
	{
		return ASanityEventInterface::class;
	}
	
	public function handlersInterface(): string
	{
		return parent::handlersInterface(); // TODO: Change the autogenerated stub
	}
}