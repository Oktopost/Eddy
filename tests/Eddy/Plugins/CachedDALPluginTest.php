<?php
namespace Eddy\Plugins;


use Eddy\DAL\CachedDAL;
use Eddy\DAL\MySQLDAL;
use Eddy\Utils\Config;

use lib\MySQLConfig;

use PHPUnit\Framework\TestCase;
use Predis\Client;


class CachedDALPluginTest extends TestCase
{
	public function test_passMySQLConfigArrayAndRedisArray()
	{
		$plugin = new CachedDALPlugin(MySQLConfig::get(), []);
		
		$config = new Config();
		
		$plugin->setup($config);
		
		self::assertInstanceOf(CachedDAL::class, $config->DAL());
	}
	
	public function test_passIMysqlConnectorAndRedisArray()
	{
		$plugin = new CachedDALPlugin(MySQLConfig::connector(), []);
		
		$config = new Config();
		
		$plugin->setup($config);
		
		self::assertInstanceOf(CachedDAL::class, $config->DAL());
	}

	/**
	 * @expectedException \Eddy\Exceptions\UnexpectedException
	 */
	public function test_passWrongValueInMySQLConfig()
	{
		$plugin = new CachedDALPlugin('a', []);
	}
}