<?php
namespace Eddy\Setup\ClassName;


use Eddy\Object\HandlerObject;

use PHPUnit\Framework\TestCase;


class HandlerBuilderTest extends TestCase
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
			class $name {}
		");
	}
	
	private function createConfig(string $name, string $handler)
	{
		eval("
			class $name implements \\Eddy\\IHandlerConfig 
			{
				public function name(): string { return 'a'; }
				public function delay(): float { return 10; }
				public function delayBuffer(): float { return 1; }
				public function packageSize(): int { return 14; }
				public function maxBulkSize(): int { return 12; }
				public function initialState(): string { return \\Eddy\\Enums\\EventState::RUNNING; }
				public function handlerClassName(): string { return $handler::class; }
				public function getInstance() {}
			}
		");
	}
	
	
	public function test_tryLoad_InvalidObject_ReturnNull()
	{
		$subject = new HandlerBuilder();
		self::assertNull($subject->tryBuild(__CLASS__));
	}
	
	public function test_tryLoad_MatchingName_ReturnObject()
	{
		$name = $this->generateName(__FUNCTION__);
		$this->createInterface("{$name}Handler");
		$this->createConfig("{$name}Config", "{$name}Handler");
		
		self::assertInstanceOf(HandlerObject::class, (new HandlerBuilder())->tryBuild("{$name}Handler"));
	}
	
	public function test_tryLoad_ConfigObjectPassed_ReturnObject()
	{
		$name = $this->generateName(__FUNCTION__);
		$this->createInterface("{$name}");
		$this->createConfig("{$name}Config", "{$name}");
		
		self::assertInstanceOf(HandlerObject::class, (new HandlerBuilder())->tryBuild("{$name}Config"));
	}
	
	public function test_tryLoad_LoadByAnnotation_ReturnObject()
	{
		$name = $this->generateName(__FUNCTION__);
		$this->createInterface("{$name}", "@config {$name}DiffName");
		$this->createConfig("{$name}DiffName", "{$name}");
		
		self::assertInstanceOf(HandlerObject::class, (new HandlerBuilder())->tryBuild("{$name}"));
	}
}