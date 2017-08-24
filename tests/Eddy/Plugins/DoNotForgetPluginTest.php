<?php
namespace Eddy\Plugins;


use Eddy\Object\HandlerObject;
use Eddy\Utils\Config;
use Eddy\Base\IExceptionHandler;
use Eddy\Object\EventObject;
use Eddy\Plugins\DoNotForget\ITarget;

use PHPUnit\Framework\TestCase;


class DoNotForgetPluginTest extends TestCase
{
	public function test_setup_ObjectAdded()
	{
		$config = new Config();
		$plugin = new DoNotForgetPlugin();
		$plugin->setup($config);
		
		self::assertEquals([$plugin], $config->Engine->Controllers);
	}
	
	
	public function test_postProcess_NoCallbacks_NoException()
	{
		$plugin = new DoNotForgetPlugin();
		$plugin->postProcess(new EventObject(), []);
	}
	
	public function test_postProcess_CallbackCalled()
	{
		$isCalled = false;
		$plugin = new DoNotForgetPlugin();
		
		$plugin->to(function () use (&$isCalled)
		{
			$isCalled = true;
		});
		
		$plugin->postProcess(new EventObject(), []);
		
		self::assertTrue($isCalled);
	}
	
	public function test_postProcess_ITargetObjectcalled()
	{
		$plugin = new DoNotForgetPlugin();
		$target = new class implements ITarget
		{
			public $isCalled = false; 
			public function flush()
			{
				$this->isCalled = true;
			}
		};
		
		$plugin->to($target);
		
		$plugin->postProcess(new EventObject(), []);
		
		self::assertTrue($target->isCalled);
	}
	
	public function test_postProcess_AllCallbacksCalled()
	{
		$isCalled1 = false;
		$isCalled2 = false;
		$plugin = new DoNotForgetPlugin();
		
		$plugin->to(function () use (&$isCalled1)
		{
			$isCalled1 = true;
		});
		
		$plugin->to(function () use (&$isCalled2)
		{
			$isCalled2 = true;
		});
		
		$plugin->postProcess(new EventObject(), []);
		
		self::assertTrue($isCalled1);
		self::assertTrue($isCalled2);
	}
	
	public function test_postProcess_ExceptionHandled()
	{
		$config = new Config();
		$config->ExceptionHandler = new class implements IExceptionHandler
		{
			public $e = null;
			public function exception(\Throwable $t): void
			{
				$this->e = $t;
			}
		};
		
		$plugin = new DoNotForgetPlugin();
		$plugin->setup($config);
		
		$plugin->to(function ()
		{
			throw new \Exception();
		});
		
		
		$plugin->postProcess(new EventObject(), []);
		
		
		self::assertInstanceOf(\Exception::class, $config->ExceptionHandler->e);
	}
	
	
	public function test_exception_CallbackCalled()
	{
		$isCalled = false;
		$plugin = new DoNotForgetPlugin();
		
		$plugin->to(function () use (&$isCalled)
		{
			$isCalled = true;
		});
		
		$plugin->exception(new HandlerObject(), [], new \Exception());
		
		self::assertTrue($isCalled);
	}
}