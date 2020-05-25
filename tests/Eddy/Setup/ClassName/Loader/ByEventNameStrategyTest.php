<?php
namespace Eddy\Setup\ClassName\Loader;


use Eddy\Objects\EventObject;
use PHPUnit\Framework\TestCase;


class ByEventNameStrategyTest extends TestCase
{
	private function createInterface(string $func, string $annotation = ''): string
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
	
	private function createConfig(string $name, ?string $event = null)
	{
		$name = 'Test' . (new \ReflectionClass($this))->getShortName() . $name;
		
		eval("
			class $name extends \\Eddy\\Event\\DynamicEventConfig 
			{
				public function __construct()
				{
					parent::__construct($event::class);
				}
			}
		");
	}
	
	
	public function test_tryLoad_ObjectIsNotEventOrHandler_ReturnNull()
	{
		$subject = new ByEventNameStrategy();
		self::assertNull($subject->tryLoad($this->createInterface(__FUNCTION__)));
	}
	
	public function test_tryLoad_NoConfigMatchingName_ReturnNull()
	{
		$subject = new ByEventNameStrategy();
		self::assertNull($subject->tryLoad($this->createInterface(__FUNCTION__ . 'Event')));
	}
	
	
	public function test_tryLoad_ConfigurationMatchingName_ConfigurationReturned()
	{
		$subject = new ByEventNameStrategy();
		$name = $this->createInterface(__FUNCTION__ . 'Event');
		$this->createConfig(__FUNCTION__ . 'Config', $name);
		
		/** @var EventObject $result */
		$result = $subject->tryLoad($name);
		
		self::assertInstanceOf(EventObject::class, $result);
		self::assertEquals($name, $result->EventInterface);
	}
	
	public function test_tryLoad_EventMarkedWithAnnotation_ConfigurationReturned()
	{
		$subject = new ByEventNameStrategy();
		$name = $this->createInterface(__FUNCTION__, '@event');
		$this->createConfig(__FUNCTION__ . 'Config', $name);
		
		self::assertInstanceOf(EventObject::class, $subject->tryLoad($name));
	}

	/**
	 * @expectedException \Eddy\Exceptions\InterfaceMismatchConfiguration
	 */
	public function test_tryLoad_ConfigurationDefinedForDifferentEventInterface_ExceptionThrown()
	{
		$subject = new ByEventNameStrategy();
		$name = $this->createInterface(__FUNCTION__, '@event');
		$nameDiff = $this->createInterface(__FUNCTION__ . 'Different', '@event');
		$this->createConfig(__FUNCTION__ . 'Config', $name . 'Different');
		
		$subject->tryLoad($name);
	}
}