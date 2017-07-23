<?php
namespace Eddy\Crawler\Base\Builder;


use Eddy\Base\IEventConfig;


interface IEventBuilder
{
	public function build(array $eventData): IEventConfig;
	public function buildAll(array $eventsData): array;
}