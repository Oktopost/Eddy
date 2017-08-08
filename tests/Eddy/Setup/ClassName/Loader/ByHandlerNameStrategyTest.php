<?php
namespace Eddy\Setup\ClassName\Loader;


use Eddy\Object\HandlerObject;
use PHPUnit\Framework\TestCase;


class ByHandlerNameStrategyTest extends TestCase
{
	private function createHandler(string $func, string $annotation = ''): string
	{
		$name = 'Test' . (new \ReflectionClass($this))->getShortName() . $func;
		
		eval("
			/**
			 * $annotation
			 */
			interface $name {}
		");
		return $name;
	}
	
	private function createConfig(string $name, ?string $handler = null)
	{
		$name = 'Test' . (new \ReflectionClass($this))->getShortName() . $name;
		
		eval("
			class $name implements \\Eddy\\IHandlerConfig 
			{
				public function name(): string { return 'a'; }
				public function delay(): float { return 10; }
				public function maxBulkSize(): int { return 12; }
				public function delayBuffer(): float { return 1; }
				public function packageSize(): int { return 14; }
				public function initialState(): string { return \\Eddy\\Enums\\EventState::RUNNING; }
				public function handlerClassName(): string { return $handler::class; }
				public function getInstance() {}
			}
		");
	}
	
	
	public function test_tryLoad_ObjectIsNotEventOrHandler_ReturnNull()
	{
		$subject = new ByHandlerNameStrategy();
		self::assertNull($subject->tryLoad($this->createHandler(__FUNCTION__)));
	}
	
	public function test_tryLoad_NoConfigMatchingName_ReturnNull()
	{
		$subject = new ByHandlerNameStrategy();
		self::assertNull($subject->tryLoad($this->createHandler(__FUNCTION__ . 'Handler')));
	}
	
	
	public function test_tryLoad_ConfigurationMatchingName_ConfigurationReturned()
	{
		$subject = new ByHandlerNameStrategy();
		$name = $this->createHandler(__FUNCTION__ . 'Handler');
		$this->createConfig(__FUNCTION__ . 'Config', $name);
		
		/** @var HandlerObject $result */
		$result = $subject->tryLoad($name);
		
		self::assertInstanceOf(HandlerObject::class, $result);
		self::assertEquals($name, $result->HandlerClassName);
	}
	
	public function test_tryLoad_HandlerMarkedWithAnnotation_ConfigurationReturned()
	{
		$subject = new ByHandlerNameStrategy();
		$name = $this->createHandler(__FUNCTION__, '@handler');
		$this->createConfig(__FUNCTION__ . 'Config', $name);
		
		self::assertInstanceOf(HandlerObject::class, $subject->tryLoad($name));
	}

	/**
	 * @expectedException \Eddy\Exceptions\HandlerMismatchConfiguration
	 */
	public function test_tryLoad_ConfigurationDefinedForDifferentHandlerClass_ExceptionThrown()
	{
		$subject = new ByHandlerNameStrategy();
		$name = $this->createHandler(__FUNCTION__, '@handler');
		$nameDiff = $this->createHandler(__FUNCTION__ . 'Different', '@handler');
		$this->createConfig(__FUNCTION__ . 'Config', $name . 'Different');
		
		$subject->tryLoad($name);
	}
}