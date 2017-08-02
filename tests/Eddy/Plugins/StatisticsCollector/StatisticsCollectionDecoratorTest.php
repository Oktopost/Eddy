<?php
namespace Eddy\Plugins\StatisticsCollector;


use Eddy\Base\Engine\IQueue;
use Eddy\Base\IEddyQueueObject;
use Eddy\Object\EventObject;
use Eddy\Plugins\StatisticsCollector\Enum\StatsOperation;
use Eddy\Plugins\StatisticsCollector\Base\IStatisticsCacheCollector;
use Eddy\Plugins\StatisticsCollector\Base\IStatisticsCollectionDecorator;

use PHPUnit\Framework\TestCase;


class StatisticsCollectionDecoratorTest extends TestCase
{
	/** @var IStatisticsCacheCollector */
	private $collector = null;
	
	/**
	 * @return \PHPUnit_Framework_MockObject_MockObject|IQueue
	 */
	private function getMockQueue(): IQueue
	{
		$queue = $this->getMockBuilder(IQueue::class)->getMock();
		
		$queue->method('dequeue')->willReturn([1]);
		
		return $queue;
	}
	
	/**
	 * @return \PHPUnit_Framework_MockObject_MockObject|IStatisticsCacheCollector
	 */
	private function getStatsCollectorMock(): IStatisticsCacheCollector
	{
		if (!$this->collector)
		{
			$this->collector = $this->getMockBuilder(IStatisticsCacheCollector::class)->getMock();
		}
		
		return $this->collector;
	}
	
	private function getSubject(): StatisticsCollectionDecorator
	{
		$decorator = \UnitTestScope::$unitSkeleton->get(IStatisticsCollectionDecorator::class);

		$decorator->child($this->getMockQueue());
		
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
	
	
	public function test_enqueue()
	{
		$collector = $this->getStatsCollectorMock();
		
		$collector->expects($this->once())
			->method('collectData')
			->with(
				$this->isInstanceOf(IEddyQueueObject::class),
				$this->equalTo(1),
				$this->equalTo(StatsOperation::ENQUEUE),
				$this->equalTo(time())
			);
		
		$this->getSubject()->enqueue([1], 0);
	}
	
	public function test_dequeue()
	{
		$collector = $this->getStatsCollectorMock();
		
		$collector->expects($this->once())
			->method('collectData')
			->with(
				$this->isInstanceOf(IEddyQueueObject::class),
				$this->equalTo(1),
				$this->equalTo(StatsOperation::DEQUEUE),
				$this->equalTo(time())
			);
		
		$this->getSubject()->dequeue(1);
	}
}