<?php
namespace Eddy\Plugins;


use Eddy\DAL\CachedDAL;
use Eddy\DAL\FallbackDAL;
use Eddy\DAL\MySQLDAL;
use Eddy\Utils\Config;

use lib\MySQLConfig;

use PHPUnit\Framework\TestCase;
use Predis\Client;


class FallbackDALPluginTest extends TestCase
{
	public function test_passMySQLConfigArrayAndRedisArray()
	{
		$plugin = new FallbackDALPlugin(MySQLConfig::get(), []);
		
		$config = new Config();
		
		$plugin->setup($config);
		
		self::assertInstanceOf(FallbackDAL::class, $config->DAL());
	}
	
	public function test_passIMysqlConnectorAndRedisArray()
	{
		$plugin = new FallbackDALPlugin(MySQLConfig::connector(), []);
		
		$config = new Config();
		
		$plugin->setup($config);
		
		self::assertInstanceOf(FallbackDAL::class, $config->DAL());
	}

	/**
	 * @expectedException \Eddy\Exceptions\UnexpectedException
	 */
	public function test_passWrongValueInMySQLConfig()
	{
		$plugin = new FallbackDALPlugin('a', []);
	}
}