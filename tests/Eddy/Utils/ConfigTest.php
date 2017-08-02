<?php
namespace Eddy\Utils;


use Eddy\Base\IDAL;
use Eddy\Base\Config\INaming;
use Eddy\Base\Config\IEngineConfig;

use Eddy\Base\IExceptionHandler;
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
}