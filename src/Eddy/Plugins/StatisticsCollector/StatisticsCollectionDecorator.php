<?php
namespace Eddy\Plugins\StatisticsCollector;


use Eddy\Base\Engine\Queue\AbstractQueueDecorator;
use Eddy\Base\IConfig;
use Eddy\Plugins\StatisticsCollector\Base\IStatisticsCollectionDecorator;


/**
 * @autoload
 */
class StatisticsCollectionDecorator extends AbstractQueueDecorator implements IStatisticsCollectionDecorator
{
	/** 
	 * @autoload
	 * @var \Eddy\Plugins\StatisticsCollector\Base\IStatisticsCacheCollector 
	 */
	private $collector;
	
	/** @var IConfig */
	private $config;

	
	public function setConfig(IConfig $config): void
	{
		$this->config = $config;
	}

	public function enqueue(array $data, float $secDelay = 0.0): void
	{
		try
		{
			$this->collector->collectEnqueue($this->getObject(), count($data));
		}
		catch (\Throwable $e)
		{
			$this->config->ExceptionHandler->exception($e);
		}
		
		$this->getQueue()->enqueue($data, $secDelay);
	}

	public function dequeue(int $maxCount, float $waitSec = 0.0): array
	{
		$data = $this->getQueue()->dequeue($maxCount, $waitSec);
		
		if (count($data) > 0)
		{
			try
			{
				$this->collector->collectDequeue($this->getObject(), count($data));
			}
			catch (\Throwable $e)
			{
				$this->config->ExceptionHandler->exception($e);
			}
		}
		
		return $data;
	}
}