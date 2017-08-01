<?php
namespace Eddy\Plugins\StatisticsCollector\Module\Storage;


use Eddy\Plugins\StatisticsCollector\Base\IStatisticsDumpStorage;


class MySQLStatsDumpStorage implements IStatisticsDumpStorage
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