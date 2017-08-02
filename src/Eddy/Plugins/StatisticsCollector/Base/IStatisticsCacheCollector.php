<?php
namespace Eddy\Plugins\StatisticsCollector\Base;


use Eddy\Base\IEddyQueueObject;


/**
 * @skeleton
 */
interface IStatisticsCacheCollector
{
	public function collectData(IEddyQueueObject $object, int $amount, string $operation, int $time): void;
	public function collectError(IEddyQueueObject $object, int $amount, int $time): void;
	public function collectExecutionTime(IEddyQueueObject $object, float $executionTime, int $time): void;
	
	public function pullData(int $endTime): array;
}