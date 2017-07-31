<?php
namespace Eddy\Plugins\StatisticsCollector\Module\Storage;


use Eddy\Plugins\StatisticsCollector\Base\IStatisticsDumpStorage;


class MySQLStatsDumpStorage implements IStatisticsDumpStorage
{
	public function isTimeToDump(): bool
	{
		// TODO: Implement isTimeToDump() method.
	}

	public function getEndTime(): int
	{
		// TODO: Implement getEndTime() method.
	}

	public function populate(array $data): void
	{
		// TODO: Implement populate() method.
	}
}