<?php
namespace Eddy\Plugins\StatisticsCollector;


use Eddy\Base\IEddyQueueObject;
use Eddy\Base\Engine\Processor\AbstractProcessController;
use Eddy\Object\HandlerObject;
use Eddy\Plugins\StatisticsCollector\Base\IStatisticsCacheCollector;
use Eddy\Scope;


class ProcessStatistics extends AbstractProcessController
{
	/** @var IStatisticsCacheCollector */
	private $collector;
	
	/** @var int */
	private $startTime = null;
	
	
	public function __construct()
	{
		$this->collector = Scope::skeleton(IStatisticsCacheCollector::class);
	}


	public function preProcess(IEddyQueueObject $target, array $payload): void
	{
		parent::preProcess($target, $payload); // TODO: Change the autogenerated stub
		
		$this->startTime = microtime(true);
	}
	
	public function postProcess(IEddyQueueObject $target, array $payload): void
	{
		parent::postProcess($target, $payload); // TODO: Change the autogenerated stub
		
		$executionTime = $this->startTime = microtime(true);
		
		$this->collector->collectExecutionTime($target, $executionTime);
	}
	
	public function exception(HandlerObject $target, array $payload, \Throwable $t): bool
	{
		$this->collector->collectError($target, sizeof($payload));
		
		return parent::exception($target, $payload, $t); // TODO: Change the autogenerated stub
	}
}