<?php
namespace CrawlerSetupTestNS\dif_namespace;


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