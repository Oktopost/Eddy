<?php
namespace Eddy\Objects;


use Eddy\Utils\Naming;
use PHPUnit\Framework\TestCase;


class HandlerObjectTest extends TestCase
{
	public function test_getQueueNaming()
	{
		$object = new HandlerObject();
		$object->Name = '123';
		$namingConfig = new Naming();
		$namingConfig->HandlerQueuePrefix = 'ABC';
		
		self::assertEquals('ABC123', $object->getQueueNaming($namingConfig));
	}

}