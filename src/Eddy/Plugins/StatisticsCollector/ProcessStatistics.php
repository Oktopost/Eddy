<?php
namespace Eddy\Plugins\StatisticsCollector;


use Eddy\Base\IConfig;
use Eddy\Base\IEddyQueueObject;
use Eddy\Base\Engine\Processor\AProcessController;
use Eddy\Objects\HandlerObject;
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
	
	/** @var IConfig */
	private $config;
	
	/** @var int */
	private $startTime = null;

	
	public function setConfig(IConfig $config): void
	{
		$this->config = $config;
	}

	public function preProcess(IEddyQueueObject $target, array $payload): void
	{
		$this->startTime = microtime(true);
	}
	
	public function postProcess(IEddyQueueObject $target, array $payload): void
	{
		$executionTime = microtime(true) - $this->startTime;
		
		try
		{
			$this->collector->collectExecutionTime($target, count($payload), $executionTime);
		}
		catch (\Throwable $e)
		{
			$this->config->ExceptionHandler->exception($e);
		}
	}
	
	public function exception(HandlerObject $target, array $payload, \Throwable $t): bool
	{
		try
		{
			$this->collector->collectError($target, count($payload));
		}
		catch (\Throwable $statsException)
		{
			$this->config->ExceptionHandler->exception($statsException);
		}
		
		return false;
	}
}