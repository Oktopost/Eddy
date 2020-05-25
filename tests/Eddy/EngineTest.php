<?php
namespace Eddy;


use Eddy\Base\IEngine;
use Eddy\Base\Engine\Publish\IPublisher;
use Eddy\Base\Engine\Publish\IPublishBuilder;

use Eddy\Engine\Proxy\DefaultProxy;
use Eddy\Objects\EventObject;
use PHPUnit\Framework\TestCase;


class EngineTest extends TestCase
{
	private function getSubject(): IEngine
	{
		$engine = Scope::skeleton(IEngine::class);
		
		return $engine;
	}

	/**
	 * @return \PHPUnit_Framework_MockObject_MockObject|IPublishBuilder
	 */
	private function createPublishBuilderMock(): IPublishBuilder
	{
		$builder = $this->getMockBuilder(IPublishBuilder::class)->getMock();
		
		$builder->method('getEventPublisher')
			->willReturn($this->createMock(IPublisher::class));
		
		return $builder;
	}
	
	
	protected function setUp()
	{
		\UnitTestScope::clear();
		
		\UnitTestScope::override(IPublishBuilder::class, $this->createPublishBuilderMock());
	}


	public function test_event_withoutProxy()
	{
		$event = new EventObject();
		$event->EventInterface = Test_Engine_DummyEventInterface::class;
		
		self::assertInstanceOf(DefaultProxy::class, $this->getSubject()->event($event));
	}
	
	public function test_event_withProxy()
	{
		$event = new EventObject();
		$event->ProxyClassName = Test_Engine_DummyEventProxy::class;
		
		self::assertInstanceOf(Test_Engine_DummyEventProxy::class, $this->getSubject()->event($event));
	}
}


class Test_Engine_DummyEventInterface {}

class Test_Engine_DummyEventProxy implements IEventProxy
{
	public function setPublisher(IPublisher $publisher): void
	{
		return;
	}
}