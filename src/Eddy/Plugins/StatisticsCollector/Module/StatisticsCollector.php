<?php
namespace Eddy\Plugins\StatisticsCollector\Module;


use Eddy\Object\EventObject;
use Eddy\Base\IEddyQueueObject;
use Eddy\Plugins\StatisticsCollector\Base\IStatisticsStorage;
use Eddy\Plugins\StatisticsCollector\Base\IStatisticsCollector;
use Eddy\Plugins\StatisticsCollector\Base\IStatisticsDumpStorage;
use Eddy\Plugins\StatisticsCollector\Enum\StatsObjectType;
use Eddy\Plugins\StatisticsCollector\Module\Storage\MySQLStatsDumpStorage;
use Eddy\Plugins\StatisticsCollector\Module\Storage\RedisStatsStorage;
use Eddy\Plugins\StatisticsCollector\Object\StatsEntry;


class StatisticsCollector implements IStatisticsCollector
{
	/** @var IStatisticsStorage  */
	private $storage;
	
	/** @var IStatisticsDumpStorage */
	private $dumpStorage;
	
	
	private function getObjectType(IEddyQueueObject $object): string
	{
		if ($object instanceof EventObject)
		{
			return StatsObjectType::EVENT;
		}
		
		return StatsObjectType::HANDLER;
	}
	
	private function prepareEntry(IEddyQueueObject $object, int $amount, string $operation, string $status): StatsEntry
	{
		$entry = new StatsEntry();
		$entry->Name = $object->Name;
		$entry->Type = $this->getObjectType($object);
		$entry->Operation = $operation;
		$entry->Status = $status;
		$entry->Amount = $amount;
		$entry->Time = time();
		
		return $entry;
	}
	
	private function dumpData(): void
	{
		$endTime = $this->dumpStorage->getEndTime();
		$data = $this->storage->pullData($endTime);
		
		if ($data)
		{
			$this->dumpStorage->populate($data);
		}
	}
	
	
	public function __construct()
	{
		$this->storage = new RedisStatsStorage();
		$this->dumpStorage = new MySQLStatsDumpStorage();
	}


	public function collect(IEddyQueueObject $object, int $amount, string $operation, string $status): void
	{
		$entry = $this->prepareEntry($object, $amount, $operation, $status);
		
		$this->storage->save($entry);
//		
//		if ($this->dumpStorage->isTimeToDump())
//		{
//			$this->dumpData();	
//		}
	}
}