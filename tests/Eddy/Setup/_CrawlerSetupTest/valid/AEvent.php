<?php
namespace CrawlerSetupTestNS\valid;


use Eddy\Event\DynamicEventConfig;


/**
 * @event AEvent
 */
interface AEventInterface
{
	
}

class AConfig extends DynamicEventConfig
{
	public function eventClassName(): string
	{
		return AEventInterface::class;
	}
}