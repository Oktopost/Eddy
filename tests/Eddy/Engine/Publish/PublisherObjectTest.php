<?php
namespace Eddy\Engine\Publish;


use Eddy\Base\DAL\IEventDAO;
use Eddy\Base\Engine\IQueue;
use Eddy\Base\Engine\ILockProvider;
use Eddy\Base\Engine\IMainQueue;
use Eddy\Base\Engine\Lock\ILocker;
use Eddy\Base\Engine\Queue\IQueueBuilder;

use Eddy\Enums\EventState;
use Eddy\Object\EventObject;

use Eddy\Scope;
use Eddy\Utils\Config;
use PHPUnit\Framework\TestCase;


class PublisherObjectTest extends TestCase
{
	/** @var \PHPUnit_Framework_MockObject_MockObject|IQueue */
	private $queue;
	
	/** @var \PHPUnit_Framework_MockObject_MockObject|IQueueBuilder */
	private $builder;
	
	/** @var \PHPUnit_Framework_MockObject_MockObject|ILocker */
	private $locker;
	
	/** @var \PHPUnit_Framework_MockObject_MockObject|IMainQueue */
	private $main;
	
	/** @var \PHPUnit_Framework_MockObject_MockObject|IEventDAO */
	private $eventDAO;
	
	/** @var EventObject */
	private $object;
	
	/** @var Config */
	private $config;
	
	
	protected function setUp()
	{
		\UnitTestScope::clear();
		
		$this->config = new Config();
		$this->config->setMainDataBase([]);
		$this->config->Engine->Locker = $this->mockILockProvider();
		
		$this->locker = $this->getMockBuilder(ILocker::class)->getMock();
		$this->config->Engine->Locker->method('get')->willReturn($this->locker);
	}
	
	
	private function assertIsObjectEnqueuedWhenObjectAtState($isEnqueued, $state)
	{
		$object = $this->object();
		$object->State = $state;
		$this->mockIEventDAO();
		
		$subject = $this->subject();
		$subject->setObject($object);
		
		if ($isEnqueued)
		{
			$this->queue->expects($this->once())->method('enqueue')->with(['a'], $this->anything());
		}
		else 
		{
			$this->queue->expects($this->never())->method('enqueue');
		}
		
		$subject->publish(['a']);
	}
	
	private function assertIsLockerCalledWhenObjectAtState($isCalled, $state)
	{
		$object = $this->object();
		$object->State = $state;
		$this->mockIEventDAO();
		
		$subject = $this->subject();
		$subject->setObject($object);
		
		if ($isCalled)
		{
			$this->locker->expects($this->once())->method('isLocked')->willReturn(false);
		}
		else 
		{
			$this->locker->expects($this->never())->method('isLocked');
		}
		
		$subject->publish(['a']);
	}
	
	private function assertIsScheduledWhenLockStateIs($isCalled, $isLocked)
	{
		$object = $this->object();
		$this->mockIEventDAO();
		
		$subject = $this->subject();
		$subject->setObject($object);
		
		$this->locker->method('isLocked')->willReturn($isLocked);
		
		if ($isCalled)
		{
			$this->main->expects($this->once())->method('schedule');
		}
		else 
		{
			$this->main->expects($this->never())->method('schedule');
		}
		
		$subject->publish(['a']);
	}
	
	
	private function mockIEventDAO()
	{
		$this->eventDAO = $this->getMockBuilder(IEventDAO::class)->getMock();
		$this->eventDAO->method('load')->willReturn($this->object);
		\UnitTestScope::override(IEventDAO::class, $this->eventDAO);
	}

	/**
	 * @return \PHPUnit_Framework_MockObject_MockObject|ILockProvider
	 */
	private function mockILockProvider()
	{
		return $this->getMockBuilder(ILockProvider::class)->getMock();
	}
	
	private function mockIQueue()
	{
		$this->queue = $this->getMockBuilder(IQueue::class)->getMock();
	}
	
	private function mockIQueueBuilder()
	{
		$this->mockIQueue();
		
		$this->builder = $this->getMockBuilder(IQueueBuilder::class)->getMock();
		$this->builder->method('getQueue')->willReturn($this->queue);
		\UnitTestScope::override(IQueueBuilder::class, $this->builder);
	}
	
	private function mockIMainQueue()
	{
		$this->main = $this->getMockBuilder(IMainQueue::class)->getMock();
		\UnitTestScope::override(IMainQueue::class, $this->main);
	}
	
	private function object($id = 1, $name = 'sub', $delay = 2): EventObject
	{
		$object = new EventObject();
		
		$object->Id		= $id;
		$object->Name	= $name;
		$object->Delay	= $delay;
		$object->State	= EventState::RUNNING;
		
		$this->object = $object;
		
		return $object;
	}
	
	private function subject(): PublisherObject
	{
		$this->mockIQueueBuilder();
		$this->mockIMainQueue();
		
		/** @var PublisherObject $subject */
		$subject = Scope::skeleton()->load(PublisherObject::class);
		$subject->setConfig($this->config);
		
		return $subject;
	}
	
	
	public function test_publish_ObjectPassedToQueueBuilder()
	{
		$object = $this->object();
		$this->mockIEventDAO();
		$this->mockIMainQueue();
		$this->mockIQueue();
		
		$builder = $this->getMockBuilder(IQueueBuilder::class)->getMock();
		\UnitTestScope::override(IQueueBuilder::class, $builder);
		
		/** @var PublisherObject $subject */
		$subject = Scope::skeleton()->load(PublisherObject::class);
		$subject->setConfig($this->config);
		$subject->setObject($object);
		
		$builder->expects($this->once())->method('getQueue')->with($object)->willReturn($this->queue);
		
		
		$subject->publish(['a']);
	}
	
	public function test_publish_ObjectPAssedToLocker()
	{
		$object = $this->object();
		$this->mockIEventDAO();
		
		$subject = $this->subject();
		$subject->setObject($object);
		
		$this->config->Engine->Locker = $this->mockILockProvider();
		$this->config->Engine->Locker
			->expects($this->once())
			->method('get')
			->with($object)
			->willReturn($this->locker);
		
		
		$subject->publish(['a']);
	}
	
	public function test_publish_ObjectPassedToScheduled()
	{
		$object = $this->object();
		$this->mockIEventDAO();
		
		$subject = $this->subject();
		$subject->setObject($object);
		
		$this->locker->method('isLocked')->willReturn(false);
		$this->main->expects($this->once())->method('schedule')->with($object);
		
		
		$subject->publish(['a']);
	}
	
	
	public function test_publish_ObjectStatePaused_DataEnququed()
	{
		$this->assertIsObjectEnqueuedWhenObjectAtState(true, EventState::PAUSED);
	}
	
	public function test_publish_ObjectStateRunning_DataEnququed()
	{
		$this->assertIsObjectEnqueuedWhenObjectAtState(true, EventState::RUNNING);
	}
	
	public function test_publish_ObjectStateDelete_DataNotEnququed()
	{
		$this->assertIsObjectEnqueuedWhenObjectAtState(false, EventState::DELETED);
	}
	
	public function test_publish_ObjectStateStopped_DataNotEnququed()
	{
		$this->assertIsObjectEnqueuedWhenObjectAtState(false, EventState::STOPPED);
	}
	
	
	public function test_publish_ObjectStateRunning_LockerCalled()
	{
		$this->assertIsLockerCalledWhenObjectAtState(true, EventState::RUNNING);
	}
	
	public function test_publish_ObjectStatePaused_LockerNotCalled()
	{
		$this->assertIsLockerCalledWhenObjectAtState(false, EventState::PAUSED);
	}
	
	
	public function test_publish_QueueLocked_QueueNotScheduled()
	{
		$this->assertIsScheduledWhenLockStateIs(false, true);
	}
	
	public function test_publish_QueueNotLocked_QueueScheduled()
	{
		$this->assertIsScheduledWhenLockStateIs(true, false);
	}
}