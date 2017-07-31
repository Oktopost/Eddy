<?php
namespace Eddy\Utils;


use Eddy\Base\IDAL;
use Eddy\Base\Config\INaming;
use Eddy\Base\Config\IEngineConfig;

use PHPUnit\Framework\TestCase;
use Squid\MySql\Impl\MySqlConnector;


class ConfigTest extends TestCase
{
	public function test_Properties()
	{
		$config = new Config();
		
		self::assertInstanceOf(INaming::class, $config->Naming);
		self::assertInstanceOf(IEngineConfig::class, $config->Engine);
	}
	
	
	public function test_setMainDataBase_Sanity()
	{
		$config = new Config();
		$config->setMainDataBase([]);
		
		self::assertInstanceOf(IDAL::class, $config->DAL());
		
		$config = new Config();
		$config->setMainDataBase(new MySqlConnector('a'));
		
		self::assertInstanceOf(IDAL::class, $config->DAL());
	}


	/**
	 * @expectedException \Eddy\Exceptions\UnexpectedException
	 */
	public function test_setMainDataBase_InvalidDataPassed()
	{
		$config = new Config();
		$config->setMainDataBase('hello world');
	}
}