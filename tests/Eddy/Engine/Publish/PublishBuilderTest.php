<?php
namespace Eddy\Engine\Publish;


use Eddy\Base\Engine\Publish\IPublisherObject;
use Eddy\Objects\EventObject;
use Eddy\Objects\HandlerObject;
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
	
	private function subject(): PublishBuilder
	{
		$obj = new PublishBuilder();
		Scope::skeleton()->context($obj, 'test');
		return $obj;
	}
	
	
	protected function setUp()
	{
		\UnitTestScope::clear();
	}
	
	
	public function test_skeleton()
	{
		$obj = new \stdClass();
		Scope::skeleton()->context($obj, 'test')->set('config', new Config());
		
		self::assertInstanceOf(PublishBuilder::class, Scope::skeleton($obj, IPublishBuilder::class));
	}
	
	
	public function test_getEventPublisher_ReturnPreparePayloadPublisherObject()
	{
		$this->mockIPublisherObject();
		$obj = $this->subject();
		
		self::assertInstanceOf(
			PreparePayloadPublisher::class, 
			$obj->getEventPublisher(new EventObject())
		);
	}
	
	public function test_getEventPublisher_ObjectPassedToPublisher()
	{
		$mock = $this->mockIPublisherObject();
		
		$obj = new PublishBuilder();
		Scope::skeleton()->context($obj, 'test')->set('config', new Config());
		$target = new EventObject();
		
		
		$mock->expects($this->once())->method('setObject')->with($target);
		
		
		$obj->getEventPublisher($target);
	}
	
	
	public function test_getHandlersPublisher_CollectionObjectReturned()
	{
		$this->mockIPublisherObject();
		
		
		$obj = new PublishBuilder();
		
		
		self::assertInstanceOf(PublishersCollection::class, $obj->getHandlersPublisher([]));
	}
	
	public function test_getHandlersPublisher_TargetObjectPassedToPublisher()
	{
		$mock = $this->mockIPublisherObject();
		
		$obj1 = new HandlerObject();
		$obj2 = new HandlerObject();
		
		$builder = new PublishBuilder();
		Scope::skeleton()->context($builder, 'test');
		
		
		$mock->expects($this->at(0))->method('setObject')->with($obj1);
		$mock->expects($this->at(1))->method('setObject')->with($obj2);
		
		$builder->getHandlersPublisher([$obj1, $obj2]);
	}
	
	public function test_getHandlersPublisher_AllPublishersPassedToCollection()
	{
		$mock = $this->mockIPublisherObject();
		
		$builder = new PublishBuilder();
		Scope::skeleton()->context($builder, 'test');
		
		
		$mock->expects($this->at(2))->method('publish')->with(['a']);
		$mock->expects($this->at(3))->method('publish')->with(['a']);
		
		$builder->getHandlersPublisher([new HandlerObject(), new HandlerObject()])->publish(['a']);
	}
}