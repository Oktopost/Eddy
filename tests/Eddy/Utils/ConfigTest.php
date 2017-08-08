<?php
namespace Eddy\Utils;


use Eddy\Base\IDAL;
use Eddy\Base\Config\INaming;
use Eddy\Base\Config\IEngineConfig;

use Eddy\Base\IExceptionHandler;
use Eddy\DAL\MySQLDAL;
use lib\MySQLConfig;
use PHPUnit\Framework\TestCase;
use PHPUnit\Runner\Exception;
use Squid\MySql\Impl\MySqlConnector;


class ConfigTest extends TestCase
{
	public function test_Properties()
	{
		$config = new Config();
		
		self::assertInstanceOf(INaming::class, $config->Naming);
		self::assertInstanceOf(IEngineConfig::class, $config->Engine);
	}
	
	
	public function test_handleError_NoHandlerSet_PHPErrorInvoked()
	{
		$ex = new Exception();
		$config = new Config();
		
		try
		{
			$config->handleError($ex);
			self::fail();
		}
		catch (\Exception $e)
		{
			self::assertEquals($ex, $e);
		}
	}
	
	
	public function test_handleError_HandlerSet_HandlerUsed()
	{
		$ex = new Exception();
		$config = new Config();
		
		$config->ExceptionHandler = new class implements IExceptionHandler
		{
			public $ex;
			
			public function exception(\Throwable $t): void
			{
				$this->ex = $t;
			}
		};
		
		$config->handleError($ex);
		
		/** @noinspection PhpUndefinedFieldInspection */
		self::assertEquals($ex, $config->ExceptionHandler->ex);
	}

	/**
	 * @expectedException \Eddy\Exceptions\InvalidUsageException
	 */
	public function test_DAL_NoDALSet()
	{
		(new Config())->DAL();
	}
	
	public function test_DAL()
	{
		$config = new Config();
		$config->setDAL(new MySQLDAL(MySQLConfig::connector()));
		
		self::assertInstanceOf(IDAL::class, $config->DAL());
	}
}