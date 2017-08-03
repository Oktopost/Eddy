<?php
namespace Eddy\Engine\Queue\DeepQueue;


use DeepQueue\DeepQueue;

use Eddy\Base\Engine\IQueue;

use PHPUnit\Framework\TestCase;


class DeepQueueAdapterTest extends TestCase
{
	/**
	 * @return \PHPUnit_Framework_MockObject_MockObject| \DeepQueue\Base\Queue\IQueue
	 */
	private function mockQueue(): \DeepQueue\Base\Queue\IQueue
	{
		return $this->getMockBuilder(\DeepQueue\Base\Queue\IQueue::class)->getMock();
	}
	
	
	private function getSubject(\DeepQueue\Base\Queue\IQueue $queue): IQueue
	{
		$dq = new Test_DQ($queue);
		
		return new DeepQueueAdapter($dq, 'test');
	}
	
	
	public function test_enqueue_DeepQueueMethodCalled()
	{
		$queue = $this->mockQueue();
		$queue->expects($this->once())
			->method('enqueueAll')
			->with($this->equalTo([1]), $this->equalTo(1));
		
		$this->getSubject($queue)->enqueue([1], 1);
	}
	
	public function test_dequeue_DeepQueueMethoCalled()
	{
		$queue = $this->mockQueue();
		$queue->expects($this->once())
			->method('dequeue')
			->with($this->equalTo(255))
			->willReturn([1]);
		
		self::assertEquals([1], $this->getSubject($queue)->dequeue(255));
	}
}


class Test_DQ extends DeepQueue
{
	private $queue;
	
	public function __construct(\DeepQueue\Base\Queue\IQueue $queue)
	{
		$this->queue = $queue;
		
		parent::__construct();
	}
	
	public function get(string $name): \DeepQueue\Base\Queue\IQueue
	{
		return $this->queue;
	}
}