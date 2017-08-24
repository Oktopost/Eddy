<?php
namespace Eddy\Plugins\StatisticsCollector;


use Eddy\Base\IEddyQueueObject;
use Eddy\Object\EventObject;
use Eddy\Object\HandlerObject;
use Eddy\Plugins\StatisticsCollector\Base\IProcessStatistics;
use Eddy\Plugins\StatisticsCollector\Base\IStatisticsCacheCollector;

use PHPUnit\Framework\TestCase;


class ProcessStatisticsTest extends TestCase
{
	/** @var IStatisticsCacheCollector */
	private $collector = null;
	
	
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
	
	private function getSubject(): ProcessStatistics
	{
		return \UnitTestScope::$unitSkeleton->get(IProcessStatistics::class);
	}
	
	private function getTarget(): IEddyQueueObject
	{
		$obj = new EventObject();
		$obj->Name = 'test';
		
		return $obj;
	}
	
	
	protected function setUp()
	{
		\UnitTestScope::clear();
		
		\UnitTestScope::override(IStatisticsCacheCollector::class, $this->getStatsCollectorMock());
	}
	
	
	public function test_preAndPostProcess()
	{
		$collector = $this->getStatsCollectorMock();
		
		$collector->expects($this->once())
			->method('collectExecutionTime')
			->with(
				$this->isInstanceOf(IEddyQueueObject::class),
				0,
				$this->greaterThan(0)
			);
		
		$this->getSubject()->preProcess($this->getTarget(), []);
		$this->getSubject()->postProcess($this->getTarget(), []);
	}
	
	public function test_exception()
	{
		$collector = $this->getStatsCollectorMock();
		
		$collector->expects($this->once())
			->method('collectError')
			->with(
				$this->isInstanceOf(IEddyQueueObject::class),
				$this->equalTo(1)
			);
		
		$this->getSubject()->exception(new HandlerObject(), [1], new \Exception());
	}
}