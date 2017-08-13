<?php
namespace Eddy\Plugins\StatisticsCollector\Module;


use Eddy\Plugins\StatisticsCollector\Base\IStatisticsStorage;


/**
 * @autoload
 */
class MySQLStatsStorage implements IStatisticsStorage
{
	private const TABLE_NAME 			= 'EddyStatistics';
	private const SETTINGS_TABLE_NAME	= 'EddyStatisticsSettings';
	private const DUMP_TIME				= 'NextDumpTime';
	
	private const TIME_DELAY			= 5;
	
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
			->upsert()
			->ignore()
			->into(self::TABLE_NAME)
			->valuesBulk($data)
			->setExp('Enqueued', 'Enqueued + VALUES(Enqueued)')
			->setExp('Dequeued', 'Dequeued + VALUES(Dequeued)')
			->setExp('WithErrors', 'WithErrors + VALUES(WithErrors)')
			->setExp('ErrorsTotal', 'ErrorsTotal + VALUES(ErrorsTotal)')
			->setExp('Processed', 'Processed + VALUES(Processed)')
			->setExp('TotalRuntime', 'TotalRuntime + VALUES(TotalRuntime)')
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
			return time() + $this->getGranularity() - self::TIME_DELAY;
		}
		
		return strtotime($date[0]) - self::TIME_DELAY;
	}
	
	private function setNextTime(int $lastTime): void
	{
		$nextDate = date('Y-m-d H:i:s', $this->roundToMinutes($lastTime) + $this->getGranularity());
		
		$this->config->mysqlConnector
			->upsert()
			->into(self::SETTINGS_TABLE_NAME, ['Param', 'Value'])
			->values([self::DUMP_TIME, $nextDate])
			->setDuplicateKeys(['Param'])
			->executeDml();
	}
	
	private function roundToMinutes(int $time): int
	{
		$dayStart = strtotime('midnight', $time);
		
		$roundedDiff = round(($time - $dayStart) / $this->getGranularity());
		
		return $dayStart + ($roundedDiff * $this->getGranularity());
	}

	private function getDataDate(int $endTime): string
	{
		return date('Y-m-d H:i:s', $this->roundToMinutes($endTime));
	}


	public function getEndTime(): int
	{
		return $this->getNextTime();
	}

	public function populate(array $data, int $endTime): void
	{
		$this->setNextTime($endTime);
		
		if (!$data) return;
		
		$preparedData = [];
		
		foreach ($data as $entry)
		{
			$entry['Granularity'] = $this->getGranularity();
			$entry['DataDate'] = $this->getDataDate(strtotime($entry['DataDate']));
			
			$preparedData[] = $entry;
		}

		$this->save($preparedData);
	}
}