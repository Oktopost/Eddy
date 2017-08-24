<?php
namespace Eddy\Plugins\StatisticsCollector\Base;


use Eddy\Base\IEddyQueueObject;


/**
 * @skeleton
 */
interface IStatisticsCacheCollector
{
	public function collectEnqueue(IEddyQueueObject $object, int $amount): void;
	public function collectDequeue(IEddyQueueObject $object, int $amount): void;
	
	public function collectError(IEddyQueueObject $object, int $amount): void;
	public function collectExecutionTime(IEddyQueueObject $object, float $executionTime): void;
	
	public function pullData(int $endTime): array;
}