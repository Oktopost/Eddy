<?php
namespace Eddy\Setup\ClassName\Utils;


use Eddy\IEventConfig;
use Eddy\Enums\EventState;
use Eddy\IHandlerConfig;
use Eddy\Objects\EventObject;

use Eddy\Objects\HandlerObject;
use PHPUnit\Framework\TestCase;


class ConfigToObjectTest extends TestCase
{
	public function test_get_PassEventConfig()
	{
		$config = new class implements IEventConfig
		{
			public function name(): string { return 'a'; }
			public function delay(): float { return 10; }
			public function maxBulkSize(): int { return 12; }
			public function delayBuffer(): float { return 1; }
			public function packageSize(): int { return 14; }
			public function initialState(): string { return EventState::RUNNING; }
			public function eventClassName(): string { return 'b'; }
			public function proxyClassName(): ?string { return 'c'; }
			public function handlersInterface(): string { return 'd'; }
			public function prepare(array $data): ?array { return null; }
		};
		
		/** @var EventObject $result */
		$result = ConfigToObject::get($config);
		
		
		self::assertInstanceOf(EventObject::class, $result);
		self::assertEquals(get_class($config), $result->ConfigClassName);
		
		self::assertEquals(EventState::RUNNING, $result->State);
		
		self::assertEquals('a',	$result->Name);
		self::assertEquals(10, 	$result->Delay);
		self::assertEquals(12, 	$result->MaxBulkSize);
		self::assertEquals(1, 	$result->DelayBuffer);
		self::assertEquals(14, 	$result->PackageSize);
		self::assertEquals('b', 	$result->EventInterface);
		self::assertEquals('c', 	$result->ProxyClassName);
		self::assertEquals('d', 	$result->HandlerInterface);
	}
	
	public function test_get_PassHandlerConfig()
	{
		$config = new class implements IHandlerConfig
		{
			public function name(): string { return 'a'; }
			public function delay(): float { return 10; }
			public function delayBuffer(): float { return 1; }
			public function packageSize(): int { return 14;	}
			public function maxBulkSize(): int { return 12; }
			public function initialState(): string { return EventState::RUNNING; }
			public function handlerClassName(): string { return 'b'; }
			public function getInstance() {}
			public function filter($item): bool 		{ return true; }
			public function convert($item)				{ return $item ;}
		};
		
		/** @var HandlerObject $result */
		$result = ConfigToObject::get($config);
		
		
		self::assertInstanceOf(HandlerObject::class, $result);
		self::assertEquals(get_class($config), $result->ConfigClassName);
		
		self::assertEquals(EventState::RUNNING, $result->State);
		
		self::assertEquals('a',	$result->Name);
		self::assertEquals(10, 	$result->Delay);
		self::assertEquals(12, 	$result->MaxBulkSize);
		self::assertEquals(1, 	$result->DelayBuffer);
		self::assertEquals(14,	$result->PackageSize);
		self::assertEquals('b',	$result->HandlerClassName);
	}
}