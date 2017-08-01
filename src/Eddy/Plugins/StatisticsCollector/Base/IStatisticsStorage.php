<?php
namespace Eddy\Plugins\StatisticsCollector\Base;


/**
 * @skeleton
 */
interface IStatisticsStorage
{
	public function isTimeToDump(): bool;
	public function getEndTime(): int;
	public function populate(array $data, int $endTime): void;
}