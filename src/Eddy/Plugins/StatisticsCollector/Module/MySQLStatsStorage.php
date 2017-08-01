<?php
namespace Eddy\Plugins\StatisticsCollector\Module;


use Eddy\Plugins\StatisticsCollector\Base\IStatisticsStorage;


/**
 * @autoload
 */
class MySQLStatsStorage implements IStatisticsStorage
{
	/**
	 * @context
	 * @var \Eddy\Plugins\StatisticsCollector\Base\IStatsConfig
	 */
	private $config;
	
	
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