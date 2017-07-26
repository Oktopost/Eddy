<?php
namespace Eddy\Engine\Publish;


use Eddy\Base\Engine\Publish\IPublisherObject;
use Eddy\Object\EventObject;
use Eddy\Object\HandlerObject;
use Eddy\Scope;
use Eddy\Base\Engine\Publish\IPublishBuilder;

use Eddy\Utils\Config;
use PHPUnit\Framework\TestCase;


class PublishBuilderTest extends TestCase
{
	/**
	 * @return \PHPUnit_Framework_MockObject_MockObject|IPublisherObject
	 */
	private function mockIPublisherObject()
	{
		$obj = $this->getMockBuilder(IPublisherObject::class)->getMock();
		\UnitTestScope::override(IPublisherObject::class, $obj);
		return $obj;
	}
	
	
	protected function setUp()
	{
		\UnitTestScope::clear();
	}
	
	
	public function test_skeleton()
	{
		self::assertInstanceOf(PublishBuilder::class, Scope::skeleton(IPublishBuilder::class));
	}
	
	
	public function test_getEventPublisher_ReturnPreparePayloadPublisherObject()
	{
		$this->mockIPublisherObject();
		
		$obj = new PublishBuilder();
		$obj->setConfig(new Config());
		
		
		self::assertInstanceOf(
			PreparePayloadPublisher::class, 
			$obj->getEventPublisher(new EventObject())
		);
	}
	
	public function test_getEventPublisher_ConfigPassedToPublisherObject()
	{
		$mock = $this->mockIPublisherObject();
		
		$config = new Config();
		$obj = new PublishBuilder();
		$obj->setConfig($config);
		
		
		$mock->expects($this->once())->method('setConfig')->with($config);
		
		
		$obj->getEventPublisher(new EventObject());
	}
	
	public function test_getEventPublisher_ObjectPassedToPublisher()
	{
		$mock = $this->mockIPublisherObject();
		
		$obj = new PublishBuilder();
		$obj->setConfig(new Config());
		
		$target = new EventObject();
		
		
		$mock->expects($this->once())->method('setObject')->with($target);
		
		
		$obj->getEventPublisher($target);
	}
	
	
	public function test_getHandlersPublisher_CollectionObjectReturned()
	{
		$this->mockIPublisherObject();
		
		
		$obj = new PublishBuilder();
		$obj->setConfig(new Config());
		
		
		self::assertInstanceOf(PublishersCollection::class, $obj->getHandlersPublisher([]));
	}
	
	public function test_getHandlersPublisher_TargetObjectPassedToPublisher()
	{
		$mock = $this->mockIPublisherObject();
		
		$obj1 = new HandlerObject();
		$obj2 = new HandlerObject();
		
		$builder = new PublishBuilder();
		$builder->setConfig(new Config());
		
		
		$mock->expects($this->at(1))->method('setObject')->with($obj1);
		$mock->expects($this->at(3))->method('setObject')->with($obj2);
		
		$builder->getHandlersPublisher([$obj1, $obj2]);
	}
	
	public function test_getHandlersPublisher_ConfigPassedToPublisher()
	{
		$mock = $this->mockIPublisherObject();
		
		$config = new Config();
		$builder = new PublishBuilder();
		$builder->setConfig(new Config());
		
		
		$mock->expects($this->at(0))->method('setConfig')->with($config);
		$mock->expects($this->at(2))->method('setConfig')->with($config);
		
		$builder->getHandlersPublisher([new HandlerObject(), new HandlerObject()]);
	}
	
	public function test_getHandlersPublisher_AllPublishersPassedToCollection()
	{
		$mock = $this->mockIPublisherObject();
		
		$builder = new PublishBuilder();
		$builder->setConfig(new Config());
		
		
		$mock->expects($this->at(4))->method('publish')->with(['a']);
		$mock->expects($this->at(5))->method('publish')->with(['a']);
		
		$builder->getHandlersPublisher([new HandlerObject(), new HandlerObject()])->publish(['a']);
	}
}