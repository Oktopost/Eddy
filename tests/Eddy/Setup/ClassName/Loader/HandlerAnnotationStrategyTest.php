<?php
namespace Eddy\Setup\ClassName\Loader;


use Eddy\Object\EventObject;
use Eddy\Object\HandlerObject;
use PHPUnit\Framework\TestCase;


class HandlerAnnotationStrategyTest extends TestCase
{
	private function generateName($func): string
	{
		return 'Test' . (new \ReflectionClass($this))->getShortName() . $func;
	}
	
	private function createClass(string $name, $annotations = ''): void
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
			class $name {}
		");
	}
	
	private function createConfig(string $name, ?string $handler = null): void
	{
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
		$subject = new HandlerAnnotationStrategy();
		$name = $this->generateName(__FUNCTION__);
		$this->createClass($name);
		
		self::assertNull($subject->tryLoad($name));
	}
	
	public function test_tryLoad_NoConfigAnnotation_ReturnNull()
	{
		$subject = new HandlerAnnotationStrategy();
		$name = $this->generateName(__FUNCTION__);
		
		$this->createClass("{$name}Handler");
		
		self::assertNull($subject->tryLoad("{$name}Handler"));
	}
	
	
	public function test_tryLoad_ConfigurationExists_ConfigurationReturned()
	{
		$subject = new HandlerAnnotationStrategy();
		$name = $this->generateName(__FUNCTION__);
		
		$this->createClass("{$name}Handler", "@config {$name}Config");
		$this->createConfig("{$name}Config", "{$name}Handler");
		
		/** @var HandlerObject $result */
		$result = $subject->tryLoad("{$name}Handler");
		
		self::assertInstanceOf(HandlerObject::class, $result);
		self::assertEquals("{$name}Handler", $result->HandlerClassName);
	}

	/**
	 * @expectedException \Eddy\Exceptions\ConfigMismatchException
	 */
	public function test_tryLoad_ConfigurationClassNotFound_ExceptionThrown()
	{
		$subject = new HandlerAnnotationStrategy();
		$name = $this->generateName(__FUNCTION__);
		
		$this->createClass("{$name}Handler", "@config {$name}Config");
		
		$subject->tryLoad("{$name}Handler");
	}

	/**
	 * @expectedException \Eddy\Exceptions\HandlerMismatchConfiguration
	 */
	public function test_tryLoad_ConfigurationDefinedForDifferentEventInterface_ExceptionThrown()
	{
		$subject = new HandlerAnnotationStrategy();
		
		$name = $this->generateName(__FUNCTION__);
		
		$this->createClass("{$name}Handler", "@config {$name}Config");
		$this->createClass("{$name}Handler1", "@config {$name}Config");
		$this->createConfig("{$name}Config", "{$name}Handler2");
		
		$subject->tryLoad("{$name}Handler");
	}
}