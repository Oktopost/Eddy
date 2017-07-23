<?php
namespace Eddy\Engine\Base\Publisher;


use Eddy\Base\IEventConfig;


interface IPublisher
{
	public function setEventConfig(IEventConfig $eventConfig): IPublisher;
	public function doWork(array $payloads): void;
}