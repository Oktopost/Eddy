<?php
namespace Eddy\Engine\Processor;


use Eddy\Base\Engine\Processor\ProcessTarget;
use Eddy\Base\Engine\Processor\IIterationProcessor;


/**
 * @context
 */
class IterationProcessor implements IIterationProcessor
{
	/**
	 * @autoload
	 * @var \Eddy\Base\Engine\Processor\IProcessControlChain
	 */
	private $chain;
	
	/**
	 * @autoload
	 * @var \Eddy\Base\Engine\IMainQueue
	 */
	private $main;

	/**
	 * @context
	 * @var \Eddy\Base\IConfig
	 */
	private $config;

	/**
	 * @autoload
	 * @var \Eddy\Base\Engine\Processor\IPayloadProcessor
	 */
	private $payloadProcessor;

	/**
	 * @autoload
	 * @var \Eddy\Base\Engine\Processor\IPayloadLoader
	 */
	private $payloadLoader;
	

	private function locker(string $queueName): bool
	{
		return $this->config->Engine->Locker->get($queueName)->lock();
	}
	
	private function reschedule(string $queueName): void
	{
		$this->unlock($queueName);
		$this->main->schedule($queueName);
	}
	
	private function unlock(string $queueName): void
	{
		$this->config->Engine->Locker->get($queueName)->unlock();
	}
	
	private function tryGetTargetOnce(float $maxWaitTime): ?ProcessTarget
	{
		$queue = $this->main->dequeue($maxWaitTime);
		
		if (!$queue || !$this->locker($queue))
		{
			return null;
		}
		
		try
		{
			$result = $this->payloadLoader->getPayloadFor($queue);
			
			if (!$result)
				$this->unlock($queue);
			
			return $result;
		}
		catch (\Throwable $e)
		{
			$this->reschedule($queue);
			throw $e;
		}
	}
	
	private function tryGetTarget(float $waitSec = 0.0): ?ProcessTarget
	{
		$now		= microtime(true);
		$endTime	= $now + $waitSec;
		$firstTime	= true;
		$target		= null;
		
		while ($firstTime || ($endTime > $now && !$target))
		{
			$maxWaitTime = max($endTime - $now, 0.0);
			$target = $this->tryGetTargetOnce($maxWaitTime);
			$firstTime = false;
			$now = microtime(true);
		}
		
		return $target;
	}
	
	private function process(ProcessTarget $target)
	{
		try
		{
			$this->payloadProcessor->process($target);
		}
		finally
		{
			$this->reschedule($target->Object->getQueueNaming($this->config->Naming));
		}
	}
	
	
	public function runOnce(): bool
	{
		if (!$this->chain->start())
			return false;
		
		// Try get target immediately without calling wait.
		$target = $this->tryGetTarget(); 
		
		// If no target found, wait for one.
		if (!$target)
		{
			$waitSec = $this->chain->waiting();
			
			if ($waitSec > 0.0)
			{
				$target = $this->tryGetTarget($waitSec);
			}
		}
		
		if ($target)
		{
			$this->process($target);
		}
		
		return true;
	}
}