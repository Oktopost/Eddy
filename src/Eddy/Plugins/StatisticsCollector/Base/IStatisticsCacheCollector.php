<?php
namespace Eddy\Plugins\StatisticsCollector\Base;


use Eddy\Base\IEddyQueueObject;
use Eddy\Plugins\StatisticsCollector\Object\StatsCachedEntry;


interface IStatisticsCacheCollector
{
	public function save(StatsCachedEntry $entry): void;
	public function collect(IEddyQueueObject $object, int $amount, string $operation, string $status): void;
	public function pullData(int $endTime): array;
}