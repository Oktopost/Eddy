<?php
namespace Eddy\Setup;


use Eddy\Scope;
use Eddy\Base\Setup\IClassNameLoader;
use Eddy\Objects\EventObject;
use Eddy\Objects\HandlerObject;
use Eddy\Base\Setup\ISetupBuilder;

use PHPUnit\Framework\TestCase;


class SetupBuilderTest extends TestCase
{
	protected function setUp()
	{
		\UnitTestScope::clear();
	}
	
	protected function tearDown()
	{
		\UnitTestScope::clear();
	}
	
	
	private function createEvent(string $name): EventObject
	{
		static $i = 0;
		$interfaceName = __FUNCTION__ . (++$i) . 'EventTestObject';
		$handlersName = __FUNCTION__ . (++$i) . 'EventTestObjectHandler';
		
		eval("interface $interfaceName {}");
		eval("interface $handlersName {}");
		
		
		$object = new EventObject();
		$object->Name = $name;
		$object->EventInterface = $interfaceName;
		$object->HandlerInterface = $handlersName;
		
		return $object;
	}
	
	
	private function createHandler(string $name, $implements): HandlerObject
	{
		static $i = 0;
		$className = __FUNCTION__ . (++$i) . 'HandlerTestObject';
		
		if ($implements instanceof EventObject)
			$implements = $implements->HandlerInterface;
		else if (!interface_exists($implements))
			eval("interface $implements {}");
		
		eval("class $className implements $implements {}");
		
		$object = new HandlerObject();
		$object->Name = $name;
		$object->HandlerClassName = $className;
		
		return $object;
	}


	/**
	 * @return \PHPUnit_Framework_MockObject_MockObject|IClassNameLoader
	 */
	private function mockClassNameLoader()
	{
		$mock = $this->getMockBuilder(IClassNameLoader::class)->getMock();
		\UnitTestScope::override(IClassNameLoader::class, $mock);
		return $mock;
	}
	
	
	public function test_sanity_SkeletonRegistered()
	{
		self::assertInstanceOf(SetupBuilder::class, Scope::skeleton(ISetupBuilder::class));
	}
	
	
	public function test_get_ReturnSetupInstance()
	{
		self::assertInstanceOf(EventSetup::class, (new SetupBuilder())->get());
	}
	
	
	public function test_add_AddEventObject_ObjectAdded()
	{
		$object = $this->createEvent('a');
		
		$builder = new SetupBuilder();
		$builder->add($object);
		
		self::assertEquals([$object], $builder->get()->Events);
	}
	
	public function test_add_AddEventObjectTwice_ObjectAddedOnlyOnce()
	{
		$object = $this->createEvent('a');
		
		$builder = new SetupBuilder();
		$builder->add($object);
		$builder->add($object);
		
		self::assertEquals([$object], $builder->get()->Events);
	}
	
	public function test_add_AddHandlerObject_ObjectAdded()
	{
		$e = $this->createEvent('a');
		$handler = $this->createHandler('a', $e);
		
		$builder = new SetupBuilder();
		$builder->add($e);
		$builder->add($handler);
		
		self::assertEquals([$handler], $builder->get()->Handlers);
	}
	
	public function test_add_AddHandlerObjectTwice_ObjectAddOnlyOnce()
	{
		$e = $this->createEvent('a');
		$handler = $this->createHandler('a', $e);
		
		$builder = new SetupBuilder();
		$builder->add($e);
		$builder->add($handler);
		$builder->add($handler);
		
		self::assertEquals([$handler], $builder->get()->Handlers);
	}
	
	public function test_add_AddByString_ClassNameLoaderUsed()
	{
		$mock = $this->mockClassNameLoader();
		$e = $this->createEvent('a');
		
		$mock->expects($this->once())->method('load')->with('a')->willReturn($e);
		
		$builder = Scope::skeleton()->load(new SetupBuilder());
		$builder->add('a');
		
		self::assertEquals([$e], $builder->get()->Events);
	}
	
	public function test_add_PassArray_AllElementsLoadedFromArray()
	{
		$mock = $this->mockClassNameLoader();
		
		$e1 = $this->createEvent('a1');
		$e2 = $this->createEvent('a2');
		$e3 = $this->createEvent('a3');
		$handler = $this->createHandler('a1', $e2);
		
		$mock->expects($this->once())->method('load')->with('a3')->willReturn($e3);
		
		/** @var SetupBuilder $builder */
		$builder = Scope::skeleton()->load(new SetupBuilder());
		$builder->add([$e1, $e2, 'a3', $handler]);
		
		self::assertEquals([$e1, $e2, $e3], $builder->get()->Events);
		self::assertEquals([$handler], $builder->get()->Handlers);
	}
	
	public function test_add_SubscribersSetupCorrectly()
	{
		$e1 = $this->createEvent('e1');
		$h1 = $this->createHandler('h1', $e1);
		$h2 = $this->createHandler('h2', $e1);
		
		$e2 = $this->createEvent('e2');
		$e3 = $this->createEvent('e3');
		$h3 = $this->createHandler('h3', $e3);
		
		$builder = new SetupBuilder();
		$builder->add([$e1, $e2, $h1, $h2, $h3, $e3]);
		
		self::assertEquals(['e1' => [ 'h1', 'h2' ], 'e3' => [ 'h3' ]], $builder->get()->Subscribers);
	}

	/**
	 * @expectedException \Eddy\Exceptions\EddyException
	 */
	public function test_add_HandlerNotRegisteredToAnyEvent()
	{
		$e1 = $this->createEvent('e1');
		$h1 = $this->createHandler('h1', $e1);
		
		$builder = new SetupBuilder();
		$builder->add($h1);
		
		$builder->get();
	}
}