<?php
namespace Eddy\Plugins\StatisticsCollector\Base;


use Predis\Client;

use Squid\MySql\IMySqlConnector;


/**
 * @property IMySqlConnector	$mysqlConnector
 * @property Client				$redisClient
 * @property int				$granularity
 */
interface IStatsConfig
{
	public function getRedisScope(): string;
}