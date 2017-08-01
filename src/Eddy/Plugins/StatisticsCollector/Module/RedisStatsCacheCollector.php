<?php
namespace Eddy\Plugins\StatisticsCollector\Module;


use Eddy\Object\EventObject;
use Eddy\Base\IEddyQueueObject;
use Eddy\Plugins\StatisticsCollector\Enum\StatsOperation;
use Eddy\Plugins\StatisticsCollector\Enum\StatsObjectType;
use Eddy\Plugins\StatisticsCollector\Base\IStatisticsCacheCollector;
use Eddy\Plugins\StatisticsCollector\Object\StatsEntry;
use Eddy\Plugins\StatisticsCollector\Utils\StatsKeyBuilder;


/**
 * @autoload
 */
class RedisStatsCacheCollector implements IStatisticsCacheCollector
{
	/** 
	 * @context
	 * @var \Eddy\Plugins\StatisticsCollector\Base\IStatsConfig
	 */
	private $config;
	
	
	private function getObjectType(IEddyQueueObject $object): string
	{
		if ($object instanceof EventObject)
		{
			return StatsObjectType::EVENT;
		}
		
		return StatsObjectType::HANDLER;
	}
	
	private function setAmountData(StatsEntry $entry, int $amount, string $operation): StatsEntry
	{
		$entry->Processed = $amount;
		
		switch ($operation)
		{
			case StatsOperation::DEQUEUE:
				$entry->Dequeued = $amount;
				break;
				
			case StatsOperation::ENQUEUE:
				$entry->Enqueued = $amount;
				break;
				
			case StatsOperation::ERROR:
				$entry->ErrorsCount = $amount;
				break;
		}
		
		return $entry;
	}
	
	private function prepareEntry(IEddyQueueObject $object, int $amount, string $operation, int $time): StatsEntry
	{
		$entry = new StatsEntry();
		$entry->Name = $object->Name;
		$entry->Type = $this->getObjectType($object);
		$entry->DataDate = date('Y-m-d H:i:s', $time);

		$entry = $this->setAmountData($entry, $amount, $operation);

		return $entry;
	}
	
	private function getKeysToPull(int $endTime): array 
	{
		return [];
	}
	
	private function save($key, array $data): void
	{
		$this->config->redisClient->hmset($key, $data);
	}
	

	public function __construct()
	{
		
	}

	
	public function collectData(IEddyQueueObject $object, int $amount, string $operation): void
	{
		$time = time();
		
		$entry = $this->prepareEntry($object, $amount, $operation, $time);
		
		$this->save(StatsKeyBuilder::get($entry, $time),  $entry->toArray());
	}
	
	public function collectError(IEddyQueueObject $object, int $amount): void
	{
		$time = time();
		
		$entry = $this->prepareEntry($object, $amount, StatsOperation::ERROR, $time);
		
		$this->save(StatsKeyBuilder::get($entry, $time),  $entry->toArray());
	}
	
	public function collectExecutionTime(IEddyQueueObject $object, float $executionTime): void
	{
		
	}

	public function pullData(int $endTime): array
	{
		$keys = $this->getKeysToPull($endTime);
	}
}