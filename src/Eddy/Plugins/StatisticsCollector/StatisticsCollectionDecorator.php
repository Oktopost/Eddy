<?php
namespace Eddy\Plugins\StatisticsCollector;


use Eddy\Base\Engine\Queue\AbstractQueueDecorator;
use Eddy\Plugins\StatisticsCollector\Enum\StatsOperation;
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
		$this->collector->collectData($this->getObject(), count($data), StatsOperation::ENQUEUE, time());
		$this->getQueue()->enqueue($data, $secDelay);
	}

	public function dequeue(int $maxCount, float $waitSec = 0.0): array
	{
		$data = $this->getQueue()->dequeue($maxCount, $waitSec);
		$this->collector->collectData($this->getObject(), count($data), StatsOperation::DEQUEUE, time());
		
		return $data;
	}
}