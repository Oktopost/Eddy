<?php
namespace Eddy\Plugins\StatisticsCollector;


use Eddy\Base\Engine\Queue\AbstractQueueDecorator;
use Eddy\Plugins\StatisticsCollector\Base\IStatisticsCacheCollector;
use Eddy\Plugins\StatisticsCollector\Base\IStatisticsCollectionDecorator;
use Eddy\Plugins\StatisticsCollector\Enum\StatsOperation;
use Eddy\Scope;


/**
 * @context
 */
class StatisticsCollectionDecorator extends AbstractQueueDecorator implements IStatisticsCollectionDecorator
{
	/** @var IStatisticsCacheCollector */
	private $collector;
	
	
	private function collect(int $amount, string $operation): void
	{
		$this->collector->collectData($this->getObject(), $amount, $operation);
	}

	
	public function __construct()
	{
		$this->collector = Scope::skeleton($this, IStatisticsCacheCollector::class);
	}
	

	public function enqueue(array $data, float $secDelay = 0.0): void
	{
		$this->collect(sizeof($data), StatsOperation::ENQUEUE);
		$this->getQueue()->enqueue($data, $secDelay);
	}

	public function dequeue(int $maxCount): array
	{
		$data = $this->getQueue()->dequeue($maxCount);
		$this->collect(sizeof($data), StatsOperation::DEQUEUE);
		
		return $data;
	}
}