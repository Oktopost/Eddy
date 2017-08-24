<?php
namespace Eddy\Plugins\StatisticsCollector;


use Eddy\Base\Engine\Queue\AbstractQueueDecorator;
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
	

	public function enqueue(array $data, float $secDelay = 0.0): void
	{
		$this->collector->collectEnqueue($this->getObject(), count($data));
		$this->getQueue()->enqueue($data, $secDelay);
	}

	public function dequeue(int $maxCount, float $waitSec = 0.0): array
	{
		$data = $this->getQueue()->dequeue($maxCount, $waitSec);
		
		if (count($data) > 0)
		{
			$this->collector->collectDequeue($this->getObject(), count($data));
		}
		
		return $data;
	}
}