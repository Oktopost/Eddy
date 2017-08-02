<?php
namespace Eddy\Plugins\StatisticsCollector\Utils;


use Eddy\Base\IEddyQueueObject;
use Eddy\Plugins\StatisticsCollector\Enum\StatsObjectType;
use Eddy\Plugins\StatisticsCollector\Object\StatsEntry;
use PHPUnit\Framework\TestCase;


class StatsDataCombinerTest extends TestCase
{
	private function getSubject(): StatsDataCombiner
	{
		return new StatsDataCombiner();
	}
	
	private function createEntry(string $name = 'test', string $type = StatsObjectType::EVENT): StatsEntry
	{
		$data = new StatsEntry();
		$data->Name = $name;
		$data->Type = $type;
		$data->DataDate = date('Y-m-d H:i:s');
		$data->WithErrors = 1;
		$data->ErrorsTotal = 1;
		$data->Granularity = 1;
		$data->Processed = 3;
		$data->Dequeued = 1;
		$data->Enqueued = 1;
		$data->TotalRuntime = 0.1;
		
		return $data;
	}
	
	private function checkEquals(StatsEntry $original, array $combined, int $multiplier = 1, ?int $granularity = null)
	{
		self::assertEquals($original->Name, $combined['Name']);
		self::assertEquals($original->Type, $combined['Type']);
		self::assertEquals($original->DataDate, $combined['DataDate']);
		
		if (!$granularity)
		{
			self::assertEquals($original->Granularity, $combined['Granularity']);
		}
		else
		{
			self::assertEquals($granularity, $combined['Granularity']);
		}
		
		self::assertEquals($original->WithErrors * $multiplier, $combined['WithErrors']);
		self::assertEquals($original->ErrorsTotal * $multiplier, $combined['ErrorsTotal']);
		self::assertEquals($original->Processed * $multiplier, $combined['Processed']);
		self::assertEquals($original->Dequeued * $multiplier, $combined['Dequeued']);
		self::assertEquals($original->Enqueued * $multiplier, $combined['Enqueued']);
		self::assertEquals($original->TotalRuntime * $multiplier, $combined['TotalRuntime']);
	}
	
	
	public function test_combine_sanity()
	{
		$data = $this->createEntry();
		
		$combined = $this->getSubject()->combine($data->toArray(), $data->toArray(), ['Granularity']);
		
		$this->checkEquals($data, $combined, 2);
	}
	
	public function test_combineAll_sanity()
	{
		$eventEntry = $this->createEntry();
		
		$eventEntry2 = $this->createEntry('test2');
		
		$handlerEntry = $this->createEntry('h1', StatsObjectType::HANDLER);
		
		$time = time();
		$granularity = 300;
		
		$combined = $this->getSubject()->combineAll([
			$eventEntry->toArray(), $eventEntry->toArray(), 
			$eventEntry2->toArray(), 
			$handlerEntry->toArray(), $handlerEntry->toArray(), $handlerEntry->toArray()
		], date('Y-m-d H:i:s', $time), $granularity);
		
		self::assertEquals(3, sizeof($combined));
		
		$this->checkEquals($eventEntry, $combined[0], 2, $granularity);
		$this->checkEquals($eventEntry2, $combined[1], 1, $granularity);
		$this->checkEquals($handlerEntry, $combined[2], 3, $granularity);
	}
}