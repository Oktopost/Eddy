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
	
	private function prepareEntry(IEddyQueueObject $object, int $amount, string $operation, int $time): StatsEntry
	{
		$entry = new StatsEntry();
		$entry->Name = $object->Name;
		$entry->Type = $this->getObjectType($object);
		$entry->Granularity = self::CACHE_GRANULARITY_TIME;
		$entry->DataDate = date('Y-m-d H:i:s', $time);

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
		
		sort($keys);
		
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

	private function save(StatsEntry $entry, int $time): void
	{
		$key = StatsKeyBuilder::get($entry->Type, $entry->Name, $time);
		$data = $entry->toArray();
		
		$oldData = $this->config->redisClient->hgetall($key);
		
		if ($oldData)
		{
			$data = (new StatsDataCombiner())->combine($oldData, $data, ['Granularity']);
		}
		
		$this->config->redisClient->hmset($key, $data);
	}

	
	public function collectData(IEddyQueueObject $object, int $amount, string $operation, int $time): void
	{
		$entry = $this->prepareEntry($object, $amount, $operation, $time);
		
		$this->save($entry, $time);
	}
	
	public function collectError(IEddyQueueObject $object, int $amount, int $time): void
	{
		$entry = $this->prepareEntry($object, $amount, StatsOperation::ERROR, $time);
		$entry->ErrorsTotal = 1;
		
		$this->save($entry, $time);
	}
	
	public function collectExecutionTime(IEddyQueueObject $object, float $executionTime, int $time): void
	{
		$entry = $this->prepareEntry($object, 0, StatsOperation::EXECUTION_TIME, $time);
		$entry->TotalRuntime = $executionTime;
		
		$this->save($entry, $time);
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