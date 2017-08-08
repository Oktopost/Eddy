<?php
namespace Eddy\Plugins;


use Eddy\DAL\MySQLDAL;
use Eddy\Utils\Config;
use lib\MySQLConfig;
use PHPUnit\Framework\TestCase;


class MySQLDALPluginTest extends TestCase
{
	public function test_passMySQLConfigArray()
	{
		$plugin = new MySQLDALPlugin(MySQLConfig::get());
		
		$config = new Config();
		
		$plugin->setup($config);
		
		self::assertInstanceOf(MySQLDAL::class, $config->DAL());
	}
	
	public function test_passIMysqlConnector()
	{
		$plugin = new MySQLDALPlugin(MySQLConfig::connector());
		
		$config = new Config();
		
		$plugin->setup($config);
		
		self::assertInstanceOf(MySQLDAL::class, $config->DAL());
	}

	/**
	 * @expectedException \Eddy\Exceptions\UnexpectedException
	 */
	public function test_passWrongValue()
	{
		$plugin = new MySQLDALPlugin('a');
	}
}