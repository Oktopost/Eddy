<?php
namespace Eddy\Engine\Queue;


use Eddy\Base\Config\INaming;
use Eddy\Base\Engine\IMainQueue;

use Eddy\Base\Engine\IQueue;
use Eddy\Base\Engine\Queue\IQueueManager;
use Eddy\Base\Engine\Queue\IQueueProvider;
use Eddy\Base\IConfig;
use Eddy\Object\EventObject;
use Eddy\Scope;
use Eddy\Utils\Config;
use Eddy\Utils\Naming;
use PHPUnit\Framework\TestCase;


class MainQueueTest extends TestCase
{
	private function getSubject(): IMainQueue
	{
		$obj = new \stdClass();
		Scope::skeleton()->context($obj, 'test')->set(IConfig::class, $this->createConfig());
		
		$mainQueue = Scope::skeleton($obj, IMainQueue::class);
		
		return $mainQueue;
	}
	
	/**
	 * @return \PHPUnit_Framework_MockObject_MockObject|IQueue
	 */
	private function getIQueueMock(): IQueue
	{
		$queue = $this->getMockBuilder(IQueue::class)->getMock();
		$queue->expects($this->once())->method('enqueue')
		->with($this->equalTo([0 => ['testNameEvent' => 'testNameEvent']], 1));
		
		return $queue;
	}
	
	/**
	 * @return \PHPUnit_Framework_MockObject_MockObject|IQueueManager
	 */
	private function getIQueueManagerMock(): IQueueManager
	{
		$manager = $this->getMockBuilder(IQueueManager::class)->getMock();
		$manager->expects($this->once())->method('getNextRuntime')->willReturn(1);
		
		return $manager;
	}
	
	/**
	 * @return \PHPUnit_Framework_MockObject_MockObject|IQueueProvider
	 */
	private function getIQueueProviderMock(): IQueueProvider
	{
		$provider = $this->getMockBuilder(IQueueProvider::class)->getMock();
		$provider->expects($this->once())->method('getQueue')
			->with($this->equalTo('testName'))
			->willReturn($this->getIQueueMock());
		
		$provider->expects($this->once())->method('getManager')
			->with($this->equalTo('testName'))
			->willReturn($this->getIQueueManagerMock());
		
		return $provider;
	}
	
	private function createConfig(): IConfig
	{
		$config = new Config();
		$config->Naming = $this->getMockBuilder(Naming::class)->getMock();
		$config->Naming->method('__get')
			->with($this->anything())
			->willReturn('testName');
		
		$config->Engine->QueueProvider = $this->getIQueueProviderMock();
		
		return $config;
	}
	
	
	protected function setUp()
	{
		\UnitTestScope::clear();
	}
	
	
	public function test_schedule()
	{
		$event = new EventObject();
		$event->Name = 'Event';
		
		$this->getSubject()->schedule($event);
	}
}