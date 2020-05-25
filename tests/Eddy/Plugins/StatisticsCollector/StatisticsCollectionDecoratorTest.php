<?php
namespace Eddy\Plugins\StatisticsCollector;


use Eddy\Base\Engine\IQueue;
use Eddy\Base\IEddyQueueObject;

use Eddy\Objects\EventObject;

use Eddy\Plugins\StatisticsCollector\Base\IStatisticsCacheCollector;
use Eddy\Plugins\StatisticsCollector\Base\IStatisticsCollectionDecorator;

use Eddy\Scope;
use PHPUnit\Framework\TestCase;


class StatisticsCollectionDecoratorTest extends TestCase
{
	/** @var IStatisticsCacheCollector */
	private $collector = null;
	
	/** @var \PHPUnit_Framework_MockObject_MockObject */
	private $queue;
	

	/**
	 * @param array|null $result
	 * @return IQueue|\PHPUnit_Framework_MockObject_MockObject
	 */
	private function getMockQueue(?array $result = null): IQueue
	{
		if ($this->queue)
			return $this->queue;
		
		$queue = $this->getMockBuilder(IQueue::class)->getMock();
		$queue->method('dequeue')->willReturn(is_null($result) ? [1] : $result);
		$this->queue = $queue;
		
		return $queue;
	}
	
	/**
	 * @return \PHPUnit_Framework_MockObject_MockObject|IStatisticsCacheCollector
	 */
	private function getStatsCollectorMock(): IStatisticsCacheCollector
	{
		if (!$this->collector)
			$this->collector = $this->getMockBuilder(IStatisticsCacheCollector::class)->getMock();
		
		return $this->collector;
	}
	
	private function getSubject(?array $result = null): StatisticsCollectionDecorator
	{
		$decorator = Scope::skeleton(IStatisticsCollectionDecorator::class);

		$decorator->child($this->getMockQueue($result));
		
		$object = new EventObject();
		$object->Name = 'test';
		
		$decorator->setObject($object);
		
		return $decorator;
	}
	
	
	protected function setUp()
	{
		\UnitTestScope::clear();
		\UnitTestScope::override(IStatisticsCacheCollector::class, $this->getStatsCollectorMock());
	}
	
	
	public function test_enqueue_CollectorCalled()
	{
		$collector = $this->getStatsCollectorMock();
		
		$collector->expects($this->once())
			->method('collectEnqueue')
			->with(
				$this->isInstanceOf(IEddyQueueObject::class),
				$this->equalTo(2)
			);
		
		$this->getSubject()->enqueue([1, 2], 0);
	}
	
	public function test_enqueue_QueueCalled()
	{
		$this->queue = $this->getMockBuilder(IQueue::class)->getMock();
		$this->queue->expects($this->once())
			->method('enqueue')
			->with([1, 2], 0);
		
		$this->getSubject()->enqueue([1, 2], 0);
	}
	
	
	public function test_dequeue_CollectorCalled()
	{
		$collector = $this->getStatsCollectorMock();
		
		$collector->expects($this->once())
			->method('collectDequeue')
			->with(
				$this->isInstanceOf(IEddyQueueObject::class),
				$this->equalTo(2)
			);
		
		$this->getSubject([1, 2])->dequeue(1);
	}
	
	public function test_dequeue_QueueCalled()
	{
		$this->queue = $this->getMockBuilder(IQueue::class)->getMock();
		$this->queue->expects($this->once())
			->method('dequeue')
			->with(123);
		
		$this->getSubject()->dequeue(123);
	}
	
	public function test_dequeue_EmptyArrayReturned_CollectNotCalled()
	{
		$collector = $this->getStatsCollectorMock();
		
		$collector->expects($this->never())
			->method('collectDequeue');
		
		$this->getSubject([])->dequeue(1);
	}
}