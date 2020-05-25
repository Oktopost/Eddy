<?php
namespace Eddy\Setup\ClassName\Loader;


use Eddy\Objects\EventObject;
use PHPUnit\Framework\TestCase;


class EventAnnotationStrategyTest extends TestCase
{
	private function generateName($func): string
	{
		return 'Test' . (new \ReflectionClass($this))->getShortName() . $func;
	}
	
	private function createInterface(string $name, $annotations = ''): void
	{
		if (!is_array($annotations))
		{
			$annotations = [$annotations];
		}
		
		$a = '';
		
		foreach ($annotations as $item)
		{
			$a .= " * $item\n";
		}
		
		eval("
			/**
			 $a
			 */
			interface $name {}
		");
	}
	
	private function createConfig(string $name, ?string $event = null): void
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
	
	
	public function test_tryLoad_ObjectIsNotEventOrHandler_ReturnNull()
	{
		$subject = new EventAnnotationStrategy();
		$name = $this->generateName(__FUNCTION__);
		$this->createInterface($name);
		
		self::assertNull($subject->tryLoad($name));
	}
	
	public function test_tryLoad_NoConfigAnnotation_ReturnNull()
	{
		$subject = new EventAnnotationStrategy();
		$name = $this->generateName(__FUNCTION__);
		
		$this->createInterface($name . 'Event');
		
		self::assertNull($subject->tryLoad("{$name}Event"));
	}
	
	
	public function test_tryLoad_ConfigurationExists_ConfigurationReturned()
	{
		$subject = new EventAnnotationStrategy();
		$name = $this->generateName(__FUNCTION__);
		
		$this->createInterface("{$name}Event", "@config {$name}Config");
		$this->createConfig("{$name}Config", "{$name}Event");
		
		/** @var EventObject $result */
		$result = $subject->tryLoad("{$name}Event");
		
		self::assertInstanceOf(EventObject::class, $result);
		self::assertEquals("{$name}Event", $result->EventInterface);
	}

	/**
	 * @expectedException \Eddy\Exceptions\ConfigMismatchException
	 */
	public function test_tryLoad_ConfigurationClassNotFound_ExceptionThrown()
	{
		$subject = new EventAnnotationStrategy();
		$name = $this->generateName(__FUNCTION__);
		
		$this->createInterface("{$name}Event", "@config {$name}Config");
		
		$subject->tryLoad("{$name}Event");
	}

	/**
	 * @expectedException \Eddy\Exceptions\InterfaceMismatchConfiguration
	 */
	public function test_tryLoad_ConfigurationDefinedForDifferentEventInterface_ExceptionThrown()
	{
		$subject = new EventAnnotationStrategy();
		
		$name = $this->generateName(__FUNCTION__);
		
		$this->createInterface("{$name}Event", "@config {$name}Config");
		$this->createInterface("{$name}Event2", "@config {$name}Config");
		$this->createConfig("{$name}Config", "{$name}Event2");
		
		$subject->tryLoad("{$name}Event");
	}
}