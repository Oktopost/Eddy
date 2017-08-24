<?php
namespace Eddy\Plugins\StatisticsCollector\Module;


use Eddy\Object\EventObject;
use Eddy\Base\IEddyQueueObject;
use Eddy\Plugins\StatisticsCollector\Enum\StatsOperation;
use Eddy\Plugins\StatisticsCollector\Enum\StatsObjectType;
use Eddy\Plugins\StatisticsCollector\Base\IStatisticsCacheCollector;
use Eddy\Plugins\StatisticsCollector\Object\StatsEntry;
use Eddy\Plugins\StatisticsCollector\Utils\StatsDataCombiner;
use Eddy\Plugins\StatisticsCollector\Utils\StatsKeyBuilder;

use Predis\Collection\Iterator\Keyspace;


/**
 * @autoload
 */
class RedisStatsCacheCollector implements IStatisticsCacheCollector
{
	private const CACHE_GRANULARITY_TIME = 1;
	
	
	/** 
	 * @context
	 * @var \Eddy\Plugins\StatisticsCollector\Base\IStatsConfig
	 */
	private $config;
	
	/** @var int|null */
	private $time = null;
	
	
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
				$entry->WithErrors = $amount;
				break;
		}
		
		return $entry;
	}
	
	private function prepareEntry(IEddyQueueObject $object, int $amount, string $operation, ?int $time = null): StatsEntry
	{
		$entry = new StatsEntry();
		
		$entry->Name		= $object->Name;
		$entry->Type		= $this->getObjectType($object);
		$entry->Granularity	= self::CACHE_GRANULARITY_TIME;
		$entry->DataDate	= date('Y-m-d H:i:s', $this->getTime($time));

		$entry = $this->setAmountData($entry, $amount, $operation);

		return $entry;
	}
	
	private function getKeysToPull(int $endTime): array 
	{
		$keys = [];
		$pattern = $this->config->getRedisScope() . '*';

		foreach (new Keyspace($this->config->redisClient, $pattern) as $key)
		{
			$time = substr($key, strrpos($key, ':') + 1);
			
			if ($time <= $endTime)
			{
				$keys[] = str_replace($this->config->getRedisScope(), '', $key);
			}
		}
		
		$keys = array_unique($keys);
		sort($keys);
		
		if (count($keys) > $this->config->maxSize)
		{
			$keys = array_slice($keys, 0, $this->config->maxSize);
		}
		
		return $keys;
	}
	
	private function getDataToPull(array $keys): array 
	{
		$transaction = $this->config->redisClient->transaction();
		
		foreach ($keys as $key)
		{
			$transaction->hgetall($key);
		}
		
		$transaction->del($keys);
		
		$result = $transaction->execute();

		array_pop($result);
		
		return $result ?: [];
	}

	private function getTime(?int $time = null): int
	{
		if ($time)
			return $time;
		
		return $this->time ?: time();
	}

	private function save(StatsEntry $entry, ?int $time = null): void
	{
		$key = StatsKeyBuilder::get($entry->Type, $entry->Name, $this->getTime($time));
		$data = $entry->toArray();
		
		$oldData = $this->config->redisClient->hgetall($key);
		
		if ($oldData)
		{
			$data = (new StatsDataCombiner())->combine($oldData, $data, ['Granularity']);
		}
		
		$this->config->redisClient->hmset($key, $data);
	}
	
	private function saveEntry(IEddyQueueObject $object, int $amount, string $operation): void
	{
		$entry = $this->prepareEntry($object, $amount, $operation);
		$this->save($entry);
	}
	
	
	public function setTime(int $time)
	{
		$this->time = $time;
	}


	public function collectEnqueue(IEddyQueueObject $object, int $amount): void
	{
		$this->saveEntry($object, $amount, StatsOperation::ENQUEUE);
	}
	
	public function collectDequeue(IEddyQueueObject $object, int $amount): void
	{
		$this->saveEntry($object, $amount, StatsOperation::DEQUEUE);
	}
	
	public function collectError(IEddyQueueObject $object, int $amount): void
	{
		$entry = $this->prepareEntry($object, $amount, StatsOperation::ERROR);
		$entry->ErrorsTotal = 1;
		
		$this->save($entry);
	}
	
	public function collectExecutionTime(IEddyQueueObject $object, float $executionTime): void
	{
		$entry = $this->prepareEntry($object, 0, StatsOperation::EXECUTION_TIME);
		$entry->TotalRuntime = $executionTime;
		
		$this->save($entry);
	}

	public function pullData(int $endTime): array
	{
		$keys = $this->getKeysToPull($endTime);
		
		if (!$keys) 
			return [];
		
		$data = $this->getDataToPull($keys);

		return $data;
	}
}