<?php
namespace Eddy\Plugins;


use Eddy\Scope;
use Eddy\IEddyPlugin;
use Eddy\Utils\Config;
use Eddy\Plugins\StatisticsCollector\Base\IStatsConfig;
use Eddy\Plugins\StatisticsCollector\Base\IProcessStatistics;
use Eddy\Plugins\StatisticsCollector\Base\IStatisticsStorage;
use Eddy\Plugins\StatisticsCollector\Base\IStatisticsCacheCollector;
use Eddy\Plugins\StatisticsCollector\Base\IStatisticsCollectionDecorator;
use Eddy\Plugins\StatisticsCollector\Config\StatsConfig;

use Squid\MySql\IMySqlConnector;


class StatisticsCollectorPlugin implements IEddyPlugin
{
	/** @var IStatsConfig */
	private $config;
	
	
	/**
	 * @param IMySqlConnector|array $mysqlConfig
	 */
	public function __construct($mysqlConfig, array $redisConfig, int $granularity = 300, int $maxSize = 10000)
	{
		$context = Scope::skeleton()->context($this, 'Eddy::StatisticsCollectorPlugin');
		
		$config = new StatsConfig($mysqlConfig, $redisConfig);
		$config->granularity = $granularity;
		
		$this->config = $config;
		$context->set(IStatsConfig::class, $config);
	}

	
	public function setup(Config $config)
	{
		/** @var IStatisticsCollectionDecorator $decorator */
		$decorator = Scope::skeleton($this, IStatisticsCollectionDecorator::class);
		$decorator->setConfig($config);
		
		$config->Engine->addDecorator($decorator);
		
		/** @var IProcessStatistics $processController */
		$processController = Scope::skeleton($this, IProcessStatistics::class);
		$processController->setConfig($config);
		
		$config->Engine->addController($processController);
	}
	
	public function dump(?int $time = null): void
	{
		/** @var IStatisticsStorage $storage */
		$storage = Scope::skeleton($this, IStatisticsStorage::class);
		
		/** @var IStatisticsCacheCollector $cache */
		$cache = Scope::skeleton($this,IStatisticsCacheCollector::class);
		
		$endTime = $time ?: $storage->getEndTime();
		
		$data = $cache->pullData($endTime);
		
		if ($data)
		{
			$lastElement = array_slice($data, -1, 1);
			$endTime = strtotime($lastElement[0]['DataDate']);
		}
		else
		{
			$endTime = time();
		}
		
		$storage->populate($data, $endTime);
		
		if (!$time)
		{
			$storage->setNextTime($endTime);
		}
	}
}