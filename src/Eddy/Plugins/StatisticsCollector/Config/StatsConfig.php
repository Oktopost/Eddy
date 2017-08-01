<?php
namespace Eddy\Plugins\StatisticsCollector\Config;


use Eddy\Plugins\StatisticsCollector\Base\IStatsConfig;

use Predis\Client;

use Squid\MySql\IMySqlConnector;


class StatsConfig implements IStatsConfig
{
	public const MAIN_PREFIX = 'Eddy:Statistics:';
	
	
	public $mysqlConnector;
	public $redisClient;

	
	/**
	 * @param array|IMySQLConnector
	 */
	private function getMySQLConnector($config): IMySqlConnector
	{
		if ($config instanceof IMySqlConnector)
		{
			return $config;
		}
		
		$mysql = \Squid::MySql();
		$mysql->config()->setConfig($config);
		
		return $mysql->getConnector();
	}
	
	private function getRedisClient(array $redisConfig): Client
	{
		$prefix = self::MAIN_PREFIX;
		
		if (isset($redisConfig['prefix']))
		{
			$prefix = $redisConfig['prefix'] . ':' . $prefix;
		}
		
		return new Client($redisConfig, ['prefix' => $prefix]);
	}


	/**
	 * @param array|IMySqlConnector $mysqlConfig
	 */
	public function __construct($mysqlConfig, array $redisConfig)
	{
		$this->mysqlConnector = $this->getMySQLConnector($mysqlConfig);
		$this->redisClient = $this->getRedisClient($redisConfig);
	}
}