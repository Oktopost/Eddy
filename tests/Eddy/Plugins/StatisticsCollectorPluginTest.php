<?php
namespace Eddy\Plugins;


use Eddy\Plugins\StatisticsCollector\Object\StatsEntry;
use Eddy\Utils\Config;
use Eddy\Plugins\StatisticsCollector\Base\IProcessStatistics;
use Eddy\Plugins\StatisticsCollector\Base\IStatisticsCacheCollector;
use Eddy\Plugins\StatisticsCollector\Base\IStatisticsCollectionDecorator;
use Eddy\Plugins\StatisticsCollector\Base\IStatisticsStorage;

use lib\MySQLConfig;

use PHPUnit\Framework\TestCase;


class StatisticsCollectorPluginTest extends TestCase
{
	/** @var IStatisticsStorage */
	private $statsStorage;
	
	/** @var IStatisticsCacheCollector */
	private $statsCache;
	
	
	private function getSubject(): StatisticsCollectorPlugin
	{
		$plugin = new StatisticsCollectorPlugin(MySQLConfig::get(), [
			'scheme'	=> 'tcp',
			'host'		=> '127.0.0.1',
			'port'		=> '6379',
			'ssl'		=> [],
			'prefix'	=> 'stats-test:'	
		]);
		
		return $plugin;
	}
	
	/**
	 * @return \PHPUnit_Framework_MockObject_MockObject|IStatisticsCollectionDecorator
	 */
	private function mockDecorator(): IStatisticsCollectionDecorator
	{
		return $this->getMockBuilder(IStatisticsCollectionDecorator::class)->getMock();
	}
	
	/**
	 * @return \PHPUnit_Framework_MockObject_MockObject|IProcessStatistics
	 */
	private function mockProcessStatistics(): IProcessStatistics
	{
		return $this->getMockBuilder(IProcessStatistics::class)->getMock();
	}
	
	/**
	 * @return \PHPUnit_Framework_MockObject_MockObject|IStatisticsStorage
	 */
	private function mockStatisticsStorage(): IStatisticsStorage
	{
		if (!$this->statsStorage)
		{
			$this->statsStorage = $this->getMockBuilder(IStatisticsStorage::class)->getMock();
		}
		
		return $this->statsStorage;
	}
	
	/**
	 * @return \PHPUnit_Framework_MockObject_MockObject|IStatisticsCacheCollector
	 */
	private function mockCacheCollector(): IStatisticsCacheCollector
	{
		if (!$this->statsCache)
		{
			$this->statsCache = $this->getMockBuilder(IStatisticsCacheCollector::class)->getMock();
		}
		
		return $this->statsCache;
	}
	
	
	protected function setUp()
	{
		\UnitTestScope::clear();
		
		\UnitTestScope::override(IStatisticsCollectionDecorator::class, $this->mockDecorator());
		\UnitTestScope::override(IProcessStatistics::class, $this->mockProcessStatistics());
		\UnitTestScope::override(IStatisticsStorage::class, $this->mockStatisticsStorage());
		\UnitTestScope::override(IStatisticsCacheCollector::class, $this->mockCacheCollector());
	}
	
	
	public function test_setup()
	{
		$config = new Config();
		$config->Engine->QueueDecorators = [];
		$config->Engine->Controllers = [];
		
		$this->getSubject()->setup($config);
		
		self::assertInstanceOf(IStatisticsCollectionDecorator::class, $config->Engine->QueueDecorators[0]);
		self::assertInstanceOf(IProcessStatistics::class, $config->Engine->Controllers[0]);
	}
	
	public function test_dump()
	{
		$time = time();
		
		$entry = new StatsEntry();
		$entry->DataDate = date('Y-m-d H:i:s', $time);

		$storage = $this->mockStatisticsStorage();
		$storage->expects($this->once())
			->method('getEndTime')->willReturn(0);
		
		$storage->expects($this->once())
			->method('populate')->with($this->equalTo([$entry->toArray()]), $this->equalTo($time));
		
		$cache = $this->mockCacheCollector();
		$cache->expects($this->once())
			->method('pullData')->with($this->equalTo(0))->willReturn([$entry->toArray()]);
		
		$this->getSubject()->dump();
	}
}