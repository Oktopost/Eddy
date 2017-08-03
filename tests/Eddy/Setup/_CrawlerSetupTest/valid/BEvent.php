<?php
namespace CrawlerSetupTestNS\valid;


use Eddy\Event\DynamicEventConfig;


/**
 * @event BEvent
 */
interface BEventInterface
{
	
}

class BConfig extends DynamicEventConfig
{
	public function eventClassName(): string
	{
		return BEventInterface::class;
	}
}