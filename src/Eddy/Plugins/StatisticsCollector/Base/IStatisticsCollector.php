<?php
namespace Eddy\Plugins\StatisticsCollector\Base;


use Eddy\Base\IEddyQueueObject;


interface IStatisticsCollector
{
	public function collect(IEddyQueueObject $object, int $amount, string $operation, string $status): void;
}