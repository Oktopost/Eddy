<?php
namespace Eddy\Plugins;


use Eddy\Plugins\StatisticsCollector\ProcessStatistics;
use Eddy\Scope;
use Eddy\IEddyPlugin;
use Eddy\Utils\Config;
use Eddy\Plugins\StatisticsCollector\Base\IStatsConfig;
use Eddy\Plugins\StatisticsCollector\Base\IStatisticsStorage;
use Eddy\Plugins\StatisticsCollector\Base\IStatisticsCacheCollector;
use Eddy\Plugins\StatisticsCollector\Config\StatsConfig;
use Eddy\Plugins\StatisticsCollector\StatisticsCollectionDecorator;

use Squid\MySql\IMySqlConnector;


class StatisticsCollectorPlugin implements IEddyPlugin
{
	/** @var IStatsConfig */
	private $config;
	
	
	/**
	 * @param IMySqlConnector|array $mysqlConfig
	 */
	public function __construct($mysqlConfig, array $redisConfig)
	{
		$context = Scope::skeleton()->context($this, 'Eddy::StatisticsCollectorPlugin');
		
		$config = new StatsConfig($mysqlConfig, $redisConfig);
		
		$this->config = $config;
		$context->set(IStatsConfig::class, $config);
	}

	
	public function setup(Config $config): void
	{
		$decorator = Scope::load($this, StatisticsCollectionDecorator::class);
		$config->Engine->addDecorator($decorator);
		
		$processController = Scope::load($this, ProcessStatistics::class);
		$config->Engine->addController($processController);
	}
	
	
	public function dump(): void
	{
		$storage = Scope::skeleton(IStatisticsStorage::class);
		$cache = Scope::skeleton(IStatisticsCacheCollector::class);
		
		$endTime = $storage->getEndTime();
		$data = $cache->pullData($endTime);
		
		if ($data)
		{
			$storage->populate($data);
		}
	}
}