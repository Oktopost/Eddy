<?php
namespace Eddy\Plugins\StatisticsCollector\Base;


use Eddy\Base\IEddyQueueObject;
use Eddy\Plugins\StatisticsCollector\Object\StatsCachedEntry;


/**
 * @skeleton
 */
interface IStatisticsCacheCollector
{
	public function collectData(IEddyQueueObject $object, int $amount, string $operation): void;
	public function collectError(IEddyQueueObject $object, int $amount): void;
	public function collectExecutionTime(IEddyQueueObject $object, float $executionTime): void;
	
	public function pullData(int $endTime): array;
}