<?php
namespace Eddy\Setup\ClassName\Loader;


use Eddy\IEventConfig;
use Eddy\IHandlerConfig;
use Eddy\Objects\EventObject;
use Eddy\Objects\HandlerObject;
use PHPUnit\Framework\TestCase;


class ConfigObjectLoaderStrategyTest extends TestCase
{
	private function createObject(string $name, string $interface = '\stdClass', string $body = '')
	{
		$name = 'TestClass' . (new \ReflectionClass($this))->getShortName() . $name;
		
		eval("
			class $name implements $interface
			{
				$body
				
				public function name(): string { return 'a'; }
				public function delay(): float { return 10; }
				public function maxBulkSize(): int { return 12; }
				public function delayBuffer(): float { return 1; }
				public function packageSize(): int { return 14; }
				public function initialState(): string { return \\Eddy\\Enums\\EventState::RUNNING; }
				public function handlerClassName(): string { return \\stdClass::class; }
				public function getInstance() {}
				public function filter(\$item): bool 		{ return true; }
				public function convert(\$item)				{ return \$item ;}
			}
		");
		
		return $name;
	}
	
	
	public function test_tryLoad_ObjectNotOfTargetInterface_ReturnNull()
	{  
		$subject = new ConfigObjectLoaderStrategy(IHandlerConfig::class);
		self::assertNull($subject->tryLoad(__CLASS__));
	}
	
	public function test_tryLoad_ObjectHasConstructorWithParams_ReturnNull()
	{
		$class = $this->createObject(
			__FUNCTION__,
			IHandlerConfig::class, 
			'public function __construct(int $i) {}'
		); 
		
		$subject = new ConfigObjectLoaderStrategy(IHandlerConfig::class);
		
		
		self::assertNull($subject->tryLoad($class));
	}
	
	public function test_tryLoad_ObjectWithoutConstructor_ReturnInstance()
	{
		$class = $this->createObject(
			__FUNCTION__,
			IHandlerConfig::class
		);
		
		$subject = new ConfigObjectLoaderStrategy(IHandlerConfig::class);
		
		self::assertInstanceOf(HandlerObject::class, $subject->tryLoad($class));
	}
	
	public function test_tryLoad_ObjectWithEmptyConstructor_ReturnInstance()
	{
		$class = $this->createObject(
			__FUNCTION__,
			IHandlerConfig::class,
			'public function __construct() {}'
		);
		
		$subject = new ConfigObjectLoaderStrategy(IHandlerConfig::class);
		
		self::assertInstanceOf(HandlerObject::class, $subject->tryLoad($class));
	}
	
	public function test_tryLoad_ObjectWithOptionalConstructorParams_ReturnInstance()
	{
		$class = $this->createObject(
			__FUNCTION__,
			IHandlerConfig::class,
			'public function __construct(int $i = 2) {}'
		);
		
		$subject = new ConfigObjectLoaderStrategy(IHandlerConfig::class);
		
		self::assertInstanceOf(HandlerObject::class, $subject->tryLoad($class));
	}

	public function test_tryLoad_NonPublicConstructor_ReturnNull()
	{
		$class = $this->createObject(
			__FUNCTION__,
			IHandlerConfig::class,
			'protected function __construct() {}'
		);
		
		$subject = new ConfigObjectLoaderStrategy(IHandlerConfig::class);
		
		self::assertNull($subject->tryLoad($class));
	}
	
	public function test_tryLoad_SanityTestForEvents()
	{
		$name = 'TestClass' . (new \ReflectionClass($this))->getShortName() . __FUNCTION__;
		
		eval("interface {$name}Event {}");
		eval("
			class $name extends \\Eddy\\Event\\DynamicEventConfig 
			{
				public function __construct()
				{
					parent::__construct({$name}Event::class);
				}
			}
		");
		
		$subject = new ConfigObjectLoaderStrategy(IEventConfig::class);
		
		self::assertInstanceOf(EventObject::class, $subject->tryLoad($name));
	}
	
	public function test_tryLoad_AbstractClass_ReturnNull()
	{
		$name = 'TestClass' . (new \ReflectionClass($this))->getShortName() . __FUNCTION__;
		
		eval("interface {$name}Event {}");
		eval("
			abstract class $name extends \\Eddy\\Event\\DynamicEventConfig 
			{
				
			}
		");
		
		$subject = new ConfigObjectLoaderStrategy(IEventConfig::class);
		
		self::assertNull($subject->tryLoad($name));
	}
}