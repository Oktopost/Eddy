<?php
namespace Eddy\Plugins\StatisticsCollector\Module;


use Eddy\Plugins\StatisticsCollector\Base\IStatisticsStorage;
use Eddy\Plugins\StatisticsCollector\Utils\StatsDataCombiner;


/**
 * @autoload
 */
class MySQLStatsStorage implements IStatisticsStorage
{
	private const TABLE_NAME 			= 'EddyStatistics';
	private const SETTINGS_TABLE_NAME	= 'EddyStatisticsSettings';
	private const DUMP_TIME				= 'NextDumpTime';
	
	
	/**
	 * @context
	 * @var \Eddy\Plugins\StatisticsCollector\Base\IStatsConfig
	 */
	private $config;
	
	
	private function getGranularity(): int 
	{
		return $this->config->granularity;
	}
	
	private function save(array $data): void
	{
		$this->config->mysqlConnector
			->insert()
			->into(self::TABLE_NAME)
			->valuesBulk($data)
			->executeDml();
	}
	
	private function getNextTime(): int
	{
		$date = $this->config->mysqlConnector
			->select()
			->column('Value')
			->from(self::SETTINGS_TABLE_NAME)
			->byField('Param', self::DUMP_TIME)
			->queryColumn();
		
		if (!$date || !isset($date[0]))
		{
			return time() - $this->getGranularity();
		}
		
		return strtotime($date[0]);
	}
	
	private function setNextTime(int $lastTime): void
	{
		$nextDate = date('Y-m-d H:i:s', $lastTime + $this->getGranularity());
		
		$this->config->mysqlConnector
			->upsert()
			->into(self::SETTINGS_TABLE_NAME, ['Param', 'Value'])
			->values([self::DUMP_TIME, $nextDate])
			->setDuplicateKeys(['Param'])
			->executeDml();
	}


	public function getEndTime(): int
	{
		return $this->getNextTime();
	}

	public function isTimeToDump(): bool
	{
		return ($this->getGranularity() <= time() - $this->getNextTime());
	}

	public function populate(array $data, int $endTime): void
	{
		$this->setNextTime($endTime);
		
		if (!$data) return;

		$newDate = date('Y-m-d H:i:s', $endTime);
		$granularity = $this->getGranularity();

		$combinedData = (new StatsDataCombiner())->combineAll($data, $newDate, $granularity);

		$this->save($combinedData);
	}
}