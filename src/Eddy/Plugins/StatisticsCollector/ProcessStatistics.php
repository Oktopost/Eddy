<?php
namespace Eddy\Plugins\StatisticsCollector;


use Eddy\Base\IEddyQueueObject;
use Eddy\Base\Engine\Processor\AProcessController;
use Eddy\Object\HandlerObject;
use Eddy\Plugins\StatisticsCollector\Base\IProcessStatistics;


/**
 * @autoload
 */
class ProcessStatistics extends AProcessController implements IProcessStatistics
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
		$executionTime = microtime(true) - $this->startTime;
		
		$this->collector->collectExecutionTime($target, count($payload), $executionTime);
	}
	
	public function exception(HandlerObject $target, array $payload, \Throwable $t): bool
	{
		$this->collector->collectError($target, count($payload));
		return true;
	}
}