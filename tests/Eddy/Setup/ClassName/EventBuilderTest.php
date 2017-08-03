<?php
namespace Eddy\Setup\ClassName;


use Eddy\Object\EventObject;

use PHPUnit\Framework\TestCase;


class EventBuilderTest extends TestCase
{
	private function generateName($func)
	{
		return 'Test' . (new \ReflectionClass($this))->getShortName() . $func;
	}
	
	
	private function createInterface(string $name, string $annotation = '')
	{
		eval("
			/**
			 * $annotation
			 */
			interface $name {}
		");
	}
	
	private function createConfig(string $name, string $event)
	{
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
	
	
	public function test_tryLoad_InvalidObject_ReturnNull()
	{
		$subject = new EventBuilder();
		self::assertNull($subject->tryBuild(__CLASS__));
	}
	
	public function test_tryLoad_MatchingName_ReturnObject()
	{
		$name = $this->generateName(__FUNCTION__);
		$this->createInterface("{$name}Event");
		$this->createConfig("{$name}Config", "{$name}Event");
		
		self::assertInstanceOf(EventObject::class, (new EventBuilder())->tryBuild("{$name}Event"));
	}
	
	public function test_tryLoad_ConfigObjectPassed_ReturnObject()
	{
		$name = $this->generateName(__FUNCTION__);
		$this->createInterface("{$name}");
		$this->createConfig("{$name}Config", "{$name}");
		
		self::assertInstanceOf(EventObject::class, (new EventBuilder())->tryBuild("{$name}Config"));
	}
	
	public function test_tryLoad_LoadByAnnotation_ReturnObject()
	{
		$name = $this->generateName(__FUNCTION__);
		$this->createInterface("{$name}", "@config {$name}DiffName");
		$this->createConfig("{$name}DiffName", "{$name}");
		
		self::assertInstanceOf(EventObject::class, (new EventBuilder())->tryBuild("{$name}"));
	}
}