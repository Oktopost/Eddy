<?php
namespace Eddy\Plugins\StatisticsCollector;


use Eddy\Base\IEddyQueueObject;
use Eddy\Base\Engine\Processor\AbstractProcessController;
use Eddy\Object\HandlerObject;


class ProcessStatistics extends AbstractProcessController
{
	public function preProcess(IEddyQueueObject $target, array $payload): void
	{
		parent::preProcess($target, $payload); // TODO: Change the autogenerated stub
	}
	
	public function postProcess(IEddyQueueObject $target, array $payload): void
	{
		parent::postProcess($target, $payload); // TODO: Change the autogenerated stub
	}
	
	public function exception(HandlerObject $target, array $payload, \Throwable $t): bool
	{
		return parent::exception($target, $payload, $t); // TODO: Change the autogenerated stub
	}
}