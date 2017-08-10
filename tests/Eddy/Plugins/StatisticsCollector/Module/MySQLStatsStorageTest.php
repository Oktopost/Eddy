<?php
namespace Eddy\Plugins\StatisticsCollector\Module;


use Eddy\Scope;
use Eddy\Plugins\StatisticsCollector\Base\IStatsConfig;
use Eddy\Plugins\StatisticsCollector\Object\StatsEntry;
use Eddy\Plugins\StatisticsCollector\Config\StatsConfig;

use lib\MySQLConfig;

use PHPUnit\Framework\TestCase;


class MySQLStatsStorageTest extends TestCase
{
	private const TABLE_NAME 			= 'EddyStatistics';
	private const SETTINGS_TABLE_NAME	= 'EddyStatisticsSettings';
	private const DUMP_TIME				= 'NextDumpTime';
	
	
		/** @var IStatsConfig */
	private $config;
	
	
	private function getSubject(): MySQLStatsStorage
	{
		$subject = Scope::skeleton()
			->load(MySQLStatsStorage::class, [IStatsConfig::class => $this->config]);
		
		return $subject;
	}
	
	private function setEndTime(int $time): void 
	{
		$this->config->mysqlConnector
			->upsert()
			->into(self::SETTINGS_TABLE_NAME, ['Param', 'Value'])
			->values([self::DUMP_TIME, date('Y-m-d H:i:s', $time)])
			->setDuplicateKeys(['Param'])
			->executeDml();
	}
	
	private function createData(): array 
	{
		$statsEntry = new StatsEntry();
		$statsEntry->Name = 'test';
		$statsEntry->Type = 'Event';
		$statsEntry->TotalRuntime = 1;
		$statsEntry->ErrorsTotal = 1;
		$statsEntry->Granularity = 1;
		$statsEntry->Enqueued = 1;
		$statsEntry->Dequeued = 1;
		$statsEntry->Processed = 3;
		$statsEntry->WithErrors = 1;
		$statsEntry->DataDate = date('Y-m-d H:i:s');
		
		return $statsEntry->toArray();
	}
	
	private function loadFromStorage(): array
	{
		return $this->config->mysqlConnector
			->select()
			->from(self::TABLE_NAME)
			->queryAll(true);
	}

	
	protected function setUp()
	{
		$redisCfg = [
			'scheme'	=> 'tcp',
			'host'		=> '127.0.0.1',
			'port'		=> '6379',
			'ssl'		=> [],
			'prefix'	=> 'stats-test:'	
		];
		
		$this->config = new StatsConfig(MySQLConfig::connector(), $redisCfg);
		
		foreach (MySQLConfig::TABLES as $table)
		{
			MySQLConfig::connector()->delete()
			->where('1 = 1')
			->from($table)
			->executeDml();
		}

		\UnitTestScope::clear();
	}
	
	
	public function test_getEndTime_NoSettings_ReturnGranularityBeforeNow()
	{
		self::assertEquals(time() - $this->config->granularity, 
			$this->getSubject()->getEndTime(), '', $this->config->granularity);
	}
	
	public function test_getEndTime_SettingExists_GotParam()
	{
		$time = time();
		$this->setEndTime($time);
		
		self::assertEquals($time, $this->getSubject()->getEndTime());
	}
	
	public function test_isTimeToDump_DiffEndNowTimeLessThanGranularity_GotFalse()
	{
		$time = time() - $this->config->granularity + ($this->config->granularity / 10);
		$this->setEndTime($time);
		
		self::assertFalse($this->getSubject()->isTimeToDump());
	}
	
	public function test_isTimeToDump_DiffEndNowTimeMoreThanGranularity_GotTrue()
	{
		$time = time() - $this->config->granularity;
		$this->setEndTime($time);
		
		self::assertTrue($this->getSubject()->isTimeToDump());
	}
	
	public function test_populate_passEmptyData_NextDateChanged()
	{
		$time = time();
		$this->setEndTime($time);
		
		$this->getSubject()->populate([], $time);
		
		self::assertEquals($time + $this->config->granularity, $this->getSubject()->getEndTime());
	}
	
	public function test_populate_passData_DataCombinedAndSaved()
	{
		$data1 = $this->createData();
		$data2 = $this->createData();
		
		$time = time();
		
		$this->getSubject()->populate([$data1, $data2], $time);
		
		$savedData = $this->loadFromStorage();
		
		self::assertNotEmpty($savedData[0]);
		
		$savedData = $savedData[0];
		
		self::assertEquals($data1['Name'], $savedData['Name']);
		self::assertEquals($data1['Type'], $savedData['Type']);
		self::assertEquals(300, $savedData['Granularity']);
		self::assertEquals($time, strtotime($savedData['DataDate']), '', $savedData['Granularity']);
		
		self::assertEquals($data1['Enqueued'] + $data2['Enqueued'], $savedData['Enqueued']);
		self::assertEquals($data1['Dequeued'] + $data2['Dequeued'], $savedData['Dequeued']);
		self::assertEquals($data1['WithErrors'] + $data2['WithErrors'], $savedData['WithErrors']);
		self::assertEquals($data1['ErrorsTotal'] + $data2['ErrorsTotal'], $savedData['ErrorsTotal']);
		self::assertEquals($data1['Processed'] + $data2['Processed'], $savedData['Processed']);
		self::assertEquals($data1['TotalRuntime'] + $data2['TotalRuntime'], $savedData['TotalRuntime']);
	}
}