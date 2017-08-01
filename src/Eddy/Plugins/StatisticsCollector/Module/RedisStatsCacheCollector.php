<?php
namespace Eddy\Plugins\StatisticsCollector\Module;


use Eddy\Base\IEddyQueueObject;
use Eddy\Object\EventObject;
use Eddy\Plugins\StatisticsCollector\Enum\StatsObjectType;
use Eddy\Plugins\StatisticsCollector\Object\StatsCachedEntry;
use Eddy\Plugins\StatisticsCollector\Base\IStatisticsCacheCollector;

use Eddy\Plugins\StatisticsCollector\Utils\StatsKeyBuilder;
use Predis\Client;


class RedisStatsCacheCollector implements IStatisticsCacheCollector
{
	/** @var Client */
	private $client;
	
	
	private function getObjectType(IEddyQueueObject $object): string
	{
		if ($object instanceof EventObject)
		{
			return StatsObjectType::EVENT;
		}
		
		return StatsObjectType::HANDLER;
	}
	
	private function prepareEntry(IEddyQueueObject $object, int $amount, string $operation, string $status): StatsCachedEntry
	{
		$entry = new StatsCachedEntry();
		$entry->Name = $object->Name;
		$entry->Type = $this->getObjectType($object);
		$entry->Operation = $operation;
		$entry->Status = $status;
		$entry->Amount = $amount;
		$entry->Time = time();
		
		return $entry;
	}
	
	private function getKeysToPull(int $endTime): array 
	{
		return [];
	}
	

	public function __construct()
	{
		
	}

	public function collect(IEddyQueueObject $object, int $amount, string $operation, string $status): void
	{
		$entry = $this->prepareEntry($object, $amount, $operation, $status);
		$this->save($entry);
	}

	public function save(StatsCachedEntry $entry): void
	{
		$this->client->hmset(StatsKeyBuilder::get($entry), $entry->toArray());
	}

	public function pullData(int $endTime): array
	{
		$keys = $this->getKeysToPull($endTime);
	}
}