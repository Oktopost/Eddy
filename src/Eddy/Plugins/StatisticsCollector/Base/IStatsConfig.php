<?php
namespace Eddy\Plugins\StatisticsCollector\Base;


use Predis\Client;
use Squid\MySql\IMySqlConnector;


/**
 * @property IMySqlConnector	$mysqlConnector
 * @property Client				$redisClient
 */
interface IStatsConfig
{
	
}