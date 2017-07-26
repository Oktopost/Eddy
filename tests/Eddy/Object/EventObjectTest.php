<?php
namespace Eddy\Object;


use Eddy\IEventConfig;
use Eddy\Utils\Naming;
use Eddy\Event\AnonymousObjectEventConfig;

use PHPUnit\Framework\TestCase;


class EventObjectTest extends TestCase
{
	public function test_getConfig_NoClassDefined_ReturnUnanimousObjectEventConfig()
	{
		$object = new EventObject();
		
		self::assertInstanceOf(AnonymousObjectEventConfig::class, $object->getConfig());
	}

	/**
	 * @expectedException \Eddy\Exceptions\ConfigMismatchException
	 */
	public function test_getConfig_NonExistingClassDefined_ExceptionThrown()
	{
		$object = new EventObject();
		
		// Just in case 1 already exists.
		$object->ConfigClassName = 'non_existing_class_2';  
		
		$object->getConfig();
	}
	
	public function test_getConfig_ClassExists_InstanceReturned()
	{
		$name = get_class($this->getMockBuilder(IEventConfig::class)->getMock());
		
		$object = new EventObject();
		$object->ConfigClassName = $name;
		
		self::assertInstanceOf($name, $object->getConfig());
	}
	
	
	public function test_getQueueNaming()
	{
		$object = new EventObject();
		$object->Name = '123';
		$namingConfig = new Naming();
		$namingConfig->EventQueuePrefix = 'abc';
		
		self::assertEquals('abc123', $object->getQueueNaming($namingConfig));
	}
}