<?php
namespace Eddy\Engine\Queue;


use Eddy\Scope;
use Eddy\Base\IConfig;
use Eddy\Base\Engine\IQueue;
use Eddy\Base\Engine\IMainQueue;
use Eddy\Base\Engine\Queue\IQueueManager;
use Eddy\Base\Engine\Queue\IQueueProvider;
use Eddy\Object\EventObject;
use Eddy\Utils\Config;
use Eddy\Utils\Naming;

use PHPUnit\Framework\TestCase;


class MainQueueTest extends TestCase
{
	/** @var IConfig */
	private $config;
	
	
	private function getSubject(IQueue $queue, ?IQueueManager $manager = null): IMainQueue
	{
		$this->config = $this->createConfig($queue, $manager);
		
		$obj = new \stdClass();
		Scope::skeleton()->context($obj, 'test')->set(IConfig::class, $this->config);
		
		$mainQueue = Scope::skeleton($obj, IMainQueue::class);
		
		return $mainQueue;
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
	private function getIQueueProviderMock(IQueue $queue, ?IQueueManager $manager = null): IQueueProvider
	{
		$provider = $this->getMockBuilder(IQueueProvider::class)->getMock();
		$provider->expects($this->once())->method('getQueue')
			->with($this->equalTo('testName'))
			->willReturn($queue);
		
		if ($manager)
		{	
			$provider->expects($this->once())->method('getManager')
				->with($this->equalTo('testName'))
				->willReturn($manager);
		}
		
		
		return $provider;
	}
	
	private function createConfig(IQueue $queue, ?IQueueManager $manager = null): IConfig
	{
		$config = new Config();
		$config->Naming = $this->getMockBuilder(Naming::class)->getMock();
		$config->Naming->method('__get')
			->with($this->anything())
			->willReturn('testName');
		
		$config->Engine->QueueProvider = $this->getIQueueProviderMock($queue, $manager);
		
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
		
		/**
		 * @var $queue \PHPUnit_Framework_MockObject_MockObject|IQueue
		 */
		$queue = $this->getMockBuilder(IQueue::class)->getMock();
		$queue
			->expects($this->once())
			->method('enqueue')
			->with($this->equalTo([0 => ['testNameEvent' => 'testNameEvent']], 1));
		
		$this->getSubject($queue, $this->getIQueueManagerMock())
			->schedule($event->getQueueNaming($this->config->Naming));
	}
	
	public function test_dequeue()
	{
		/**
		 * @var $queue \PHPUnit_Framework_MockObject_MockObject|IQueue
		 */
		$queue = $this->getMockBuilder(IQueue::class)->getMock();
		$queue
			->expects($this->once())
			->method('dequeue')
			->with($this->equalTo(1))
			->willReturn([1 => 'a']);
		
		self::assertEquals('a', $this->getSubject($queue)->dequeue());
	}
	
	public function test_dequeue_emptyArray()
	{
		/**
		 * @var $queue \PHPUnit_Framework_MockObject_MockObject|IQueue
		 */
		$queue = $this->getMockBuilder(IQueue::class)->getMock();
		$queue
			->expects($this->once())
			->method('dequeue')
			->with($this->equalTo(1))
			->willReturn([]);
		
		self::assertEmpty($this->getSubject($queue)->dequeue());
	}

	/**
	 * @expectedException \Eddy\Exceptions\AbortException
	 */
	public function test_dequeue_withAbort()
	{
		/**
		 * @var $queue \PHPUnit_Framework_MockObject_MockObject|IQueue
		 */
		$queue = $this->getMockBuilder(IQueue::class)->getMock();
		$queue
			->expects($this->once())
			->method('dequeue')
			->with($this->equalTo(1))
			->willReturn([MainQueue::ABORT_INDICATOR]);
		
		$this->getSubject($queue)->dequeue();
	}
	
	public function test_sendAbort()
	{
		/**
		 * @var $queue \PHPUnit_Framework_MockObject_MockObject|IQueue
		 */
		$queue = $this->getMockBuilder(IQueue::class)->getMock();
		$queue
			->expects($this->once())
			->method('enqueue')
			->with($this->equalTo([MainQueue::ABORT_INDICATOR, MainQueue::ABORT_INDICATOR]));
		
		$this->getSubject($queue)->sendAbort(2);
	}
}