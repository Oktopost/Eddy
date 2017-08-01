<?php
namespace Eddy\Plugins\StatisticsCollector\Base;


interface IStatisticsStorage
{
	public function isTimeToDump(): bool;
	public function getEndTime(): int;
	public function populate(array $data): void;
}