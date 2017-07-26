<?php
namespace Eddy\Event;


use Eddy\Enums\EventState;
use Eddy\Object\EventObject;
use PHPUnit\Framework\TestCase;


class AnonymousObjectEventConfigTest extends TestCase
{
	public function test_sanity()
	{
		$object = new EventObject();
		$config = new AnonymousObjectEventConfig($object);
		
		$object->Name				= 'NAME';
		$object->Delay				= 0.54;
		$object->ProxyClassName		= 'abc';
		$object->EventInterface		= 'evn';
		$object->HandlerInterface	= 'hand';
		
		self::assertEquals($config->name(),					$object->Name);
		self::assertEquals($config->delay(),				$object->Delay);
		self::assertEquals($config->proxyClassName(),		$object->ProxyClassName);
		self::assertEquals($config->eventClassName(),		$object->EventInterface);
		self::assertEquals($config->handlersInterface(),	$object->HandlerInterface);
		
		self::assertEquals($config->initialState(),	EventState::RUNNING);
		
		self::assertNull($config->prepare(['a', 'b']));
	}
}