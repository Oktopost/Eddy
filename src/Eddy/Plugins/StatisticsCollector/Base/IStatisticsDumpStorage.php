<?php
namespace Eddy\Plugins\StatisticsCollector\Base;


interface IStatisticsDumpStorage
{
	public function isTimeToDump(): bool;
	public function getEndTime(): int;
	public function populate(array $data): void;
}