<?php
namespace Eddy\Engine\Queue;


use Eddy\Scope;
use Eddy\Base\IConfig;
use Eddy\Base\IEddyQueueObject;
use Eddy\Base\Engine\IQueue;
use Eddy\Base\Engine\Queue\IQueueBuilder;
use Eddy\Base\Engine\Queue\IQueueProvider;
use Eddy\Utils\Config;
use Eddy\Utils\Naming;
use Eddy\Object\EventObject;
use Eddy\Plugins\ExecutorLoggerPlugin;
use Eddy\Plugins\StatisticsCollector\Base\IStatisticsCollectionDecorator;

use PHPUnit\Framework\TestCase;


class QueueBuilderTest extends TestCase
{
	private function getSubject(array $decorators): IQueueBuilder
	{
		$obj = new \stdClass();
		Scope::skeleton()->context($obj, 'test')->set(IConfig::class, $this->createConfig($decorators));
		
		return Scope::skeleton($obj, IQueueBuilder::class);
	}
	
	/**
	 * @return \PHPUnit_Framework_MockObject_MockObject|IQueue
	 */
	private function getIQueueMock(): IQueue
	{
		return $this->getMockBuilder(IQueue::class)->getMock();
	}
	
	/**
	 * @return \PHPUnit_Framework_MockObject_MockObject|IQueueProvider
	 */
	private function getIQueueProviderMock(): IQueueProvider
	{
		$provider = $this->getMockBuilder(IQueueProvider::class)->getMock();
		$provider->expects($this->any())->method('getQueue')
			->with($this->equalTo('testNameEvent'))
			->willReturn($this->getIQueueMock());

		return $provider;
	}
	
	private function createConfig(array $decorators): IConfig
	{
		$config = new Config();
		$config->Naming = $this->getMockBuilder(Naming::class)->getMock();
		$config->Naming->method('__get')
			->with($this->anything())
			->willReturn('testName');
		
		$config->Engine->QueueProvider = $this->getIQueueProviderMock();
		$config->Engine->QueueDecorators = $decorators;
		
		return $config;
	}
	
	private function getEvent(): IEddyQueueObject
	{
		$event = new EventObject();
		$event->Name = 'Event';
		
		return $event;
	}
	
	
	protected function setUp()
	{
		\UnitTestScope::clear();
	}
	
	
	public function test_getQueue_NoDecorators()
	{
		$this->getSubject([])->getQueue($this->getEvent());
	}
	
	public function test_getQueue_WithDecorators()
	{
		$this->getSubject([new ExecutorLoggerPlugin(), IStatisticsCollectionDecorator::class])
			->getQueue($this->getEvent());
	}

	/**
	 * @expectedException \Eddy\Exceptions\UnexpectedException
	 */
	public function test_getQueue_WrongDecorator()
	{
		$this->getSubject([1])->getQueue($this->getEvent());
	}
}