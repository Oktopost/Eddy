<?php
namespace Eddy\Plugins\StatisticsCollector;


use Eddy\Scope;
use Eddy\Base\Engine\Queue\AbstractQueueDecorator;
use Eddy\Plugins\StatisticsCollector\Base\IStatisticsCacheCollector;
use Eddy\Plugins\StatisticsCollector\Enum\StatsStatus;
use Eddy\Plugins\StatisticsCollector\Enum\StatsOperation;


class StatisticsCollectionDecorator extends AbstractQueueDecorator
{
	/** @var IStatisticsCacheCollector */
	private $collector;
	
	
	private function collect(int $amount, string $operation, string $state): void
	{
		$this->collector->collect($this->getObject(), $amount, $operation, $state);
	}
	
	
	public function __construct()
	{
		$this->collector = Scope::skeleton(IStatisticsCacheCollector::class);
	}


	public function enqueue(array $data, float $secDelay = 0.0): void
	{
		$this->collect(sizeof($data), StatsOperation::ENQUEUE, StatsStatus::SUCCESS);
		$this->getQueue()->enqueue($data, $secDelay);
	}

	public function dequeue(int $maxCount): array
	{
		$data = $this->getQueue()->dequeue($maxCount);
		$this->collect(sizeof($data), StatsOperation::DEQUEUE, StatsStatus::SUCCESS);
		
		return $data;
	}
}