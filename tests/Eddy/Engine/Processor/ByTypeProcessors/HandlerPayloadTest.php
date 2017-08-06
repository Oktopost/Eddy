<?php
namespace Eddy\Engine\Processor\ByTypeProcessors;


use Eddy\Base\Module\IHandlersModule;
use Eddy\Base\Engine\Processor\ProcessTarget;
use Eddy\Exceptions\EddyException;
use Eddy\Utils\Config;
use Eddy\Object\HandlerObject;

use PHPUnit\Framework\TestCase;


class HandlerPayloadTest extends TestCase
{
	private function handler(string $name): HandlerObject
	{
		$handler = new HandlerObject();
		$handler->HandlerClassName = $name;
		return $handler;
	}
	
	private function target($handler, array $payload = [['a']]): ProcessTarget
	{
		if (is_string($handler))
			$handler = $this->handler($handler);
		
		$target = new ProcessTarget();
		$target->Payload = $payload;
		$target->Object = $handler;
		
		return $target;
	}
	
	private function subject(): HandlerPayload
	{
		$config = new Config();
		return \UnitTestScope::load(HandlerPayload::class, ['config' => $config]);
	}
	
	
	protected function setUp()
	{
		\UnitTestScope::clear();
	}
	
	protected function tearDown()
	{
		\UnitTestScope::clear();
	}
	
	
	public function test_HandlerClassDoesNotExists_PauseCalledForHandlerPayload()
	{
		$target = $this->target('Non_ExistingClassName');
		
		$module = $this->getMockBuilder(IHandlersModule::class)->getMock();
		\UnitTestScope::override(IHandlersModule::class, $module);
		$module->expects($this->once())->method('pause')->with($target->Object);
		
		try
		{
			$this->subject()->process($target);
		}
		catch (\ReflectionException $e) {}
	}

	/**
	 * @expectedException \ReflectionException
	 */
	public function test_HandlerClassDoesNotExists_ExceptionThrown()
	{
		$target = $this->target('Non_ExistingClassName');
		
		$module = $this->getMockBuilder(IHandlersModule::class)->getMock();
		\UnitTestScope::override(IHandlersModule::class, $module);
		
		$this->subject()->process($target);
	}
	
	
	/**
	 * @expectedException \Eddy\Exceptions\EddyException
	 */
	public function test_HandlerClassHaveNoMethod_ExceptionThrown()
	{
		$class = new class 
		{
			public function handleEmpty() {}
			public function handleNotArray($a) {}
			public function handleDefault(array $b = []) {}
			public function handleMoreParams(array $a, int $b) {}
			public function handleNotPublic(array $a, int $b) {}
		};
		
		$target = $this->target(get_class($class));
		
		$module = $this->getMockBuilder(IHandlersModule::class)->getMock();
		\UnitTestScope::override(IHandlersModule::class, $module);
		
		$this->subject()->process($target);
	}
	
	public function test_HandlerClassHaveNoMethod_PauseInvoked()
	{
		$class = new class 
		{
			public function handleEmpty() {}
			public function handleNotArray($a) {}
			public function handleDefault(array $b = []) {}
			public function handleMoreParams(array $a, int $b) {}
			public function handleNotPublic(array $a, int $b) {}
		};
		
		$target = $this->target(get_class($class));
		
		$module = $this->getMockBuilder(IHandlersModule::class)->getMock();
		\UnitTestScope::override(IHandlersModule::class, $module);
		$module->expects($this->once())->method('pause')->with($target->Object);
		
		try 
		{
			$this->subject()->process($target);
		}
		catch (EddyException $e) {}
	}
	
	public function test_HandlerClassHaveMethod_MethodCalled()
	{
		$class = new class 
		{
			public static $value;
			public function handleEmpty(array $item) { self::$value = $item; }
		};
		
		$target = $this->target(get_class($class));
		
		$module = $this->getMockBuilder(IHandlersModule::class)->getMock();
		\UnitTestScope::override(IHandlersModule::class, $module);
		
		$this->subject()->process($target);
		
		self::assertEquals($target->Payload, $class::$value);
	}
}