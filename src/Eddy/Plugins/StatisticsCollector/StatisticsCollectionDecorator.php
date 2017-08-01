<?php
namespace Eddy\Plugins\StatisticsCollector;


use Eddy\Base\IEddyQueueObject;
use Eddy\Base\Engine\IQueue;
use Eddy\Base\Engine\Queue\IQueueDecorator;
use Eddy\Plugins\StatisticsCollector\Base\IStatisticsCollector;
use Eddy\Plugins\StatisticsCollector\Enum\StatsStatus;
use Eddy\Plugins\StatisticsCollector\Enum\StatsOperation;
use Eddy\Plugins\StatisticsCollector\Module\StatisticsCollector;


class StatisticsCollectionDecorator implements IQueueDecorator
{
	/** @var IQueue */
	private $childQueue = null;
	
	/** @var IEddyQueueObject */
	private $object = null;
	
	/** @var IStatisticsCollector */
	private $collector;
	
	
	private function collect(int $amount, string $operation, string $state): void
	{
		$this->collector->collect($this->object, $amount, $operation, $state);
	}
	
	
	public function __construct()
	{
		$this->collector = new StatisticsCollector();
	}


	public function enqueue(array $data, float $secDelay = 0.0): void
	{
		$this->collect(sizeof($data), StatsOperation::ENQUEUE, StatsStatus::SUCCESS);
		$this->childQueue->enqueue($data, $secDelay);
	}

	public function dequeue(int $maxCount): array
	{
		$data = $this->childQueue->dequeue($maxCount);
		$this->collect(sizeof($data), StatsOperation::DEQUEUE, StatsStatus::SUCCESS);
		
		return $data;
	}

	public function child(IQueue $queue): void
	{
		$this->childQueue = $queue;
	}

	public function setObject(IEddyQueueObject $object): void
	{
		$this->object = $object;
	}
}