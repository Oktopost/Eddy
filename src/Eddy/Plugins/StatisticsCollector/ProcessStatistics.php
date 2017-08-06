<?php
namespace Eddy\Plugins\StatisticsCollector;


use Eddy\Base\IEddyQueueObject;
use Eddy\Base\Engine\Processor\AbstractProcessController;
use Eddy\Object\HandlerObject;
use Eddy\Plugins\StatisticsCollector\Base\IProcessStatistics;


/**
 * @autoload
 */
class ProcessStatistics extends AbstractProcessController implements IProcessStatistics
{
	/** 
	 * @autoload
	 * @var \Eddy\Plugins\StatisticsCollector\Base\IStatisticsCacheCollector 
	 */
	private $collector;
	
	/** @var int */
	private $startTime = null;


	public function preProcess(IEddyQueueObject $target, array $payload): void
	{
		$this->startTime = microtime(true);
	}
	
	public function postProcess(IEddyQueueObject $target, array $payload): void
	{
		$executionTime = $this->startTime = microtime(true);
		
		$this->collector->collectExecutionTime($target, $executionTime, time());
	}
	
	public function exception(HandlerObject $target, array $payload, \Throwable $t): bool
	{
		$this->collector->collectError($target, sizeof($payload), time());
		
		return true;
	}
}