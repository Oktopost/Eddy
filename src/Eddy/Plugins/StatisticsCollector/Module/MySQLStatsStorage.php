<?php
namespace Eddy\Plugins\StatisticsCollector\Module;


use Eddy\Plugins\StatisticsCollector\Base\IStatisticsStorage;


class MySQLStatsStorage implements IStatisticsStorage
{
	public function isTimeToDump(): bool
	{
		return true;
	}

	public function getEndTime(): int
	{
		return time();
	}

	public function populate(array $data): void
	{
		// TODO: Implement populate() method.
	}
}