<?php
namespace Eddy\Plugins\StatisticsCollector\Base;


use Eddy\Plugins\StatisticsCollector\Object\StatsEntry;


interface IStatisticsStorage
{
	public function save(StatsEntry $entry): void;
	public function pullData(int $endTime): array;
}