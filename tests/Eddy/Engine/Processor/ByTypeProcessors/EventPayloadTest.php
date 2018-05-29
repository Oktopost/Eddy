<?php
namespace Eddy\Engine\Processor\ByTypeProcessors;


use Eddy\Base\IConfig;
use Eddy\Base\Engine\IQueue;
use Eddy\Base\Engine\IMainQueue;
use Eddy\Base\Engine\Queue\IQueueProvider;
use Eddy\Base\Engine\Processor\ProcessTarget;
use Eddy\Base\Module\ISubscribersModule;
use Eddy\Handler\AbstractHandlerConfig;
use Eddy\IHandlerConfig;
use Eddy\Utils\Config;
use Eddy\Utils\Naming;
use Eddy\Enums\EventState;
use Eddy\Object\EventObject;
use Eddy\Object\HandlerObject;

use PHPUnit\Framework\TestCase;


class EventPayloadTest extends TestCase
{
	/** @var \PHPUnit_Framework_MockObject_MockObject|ISubscribersModule */
	private $subModule;
	
	/** @var \PHPUnit_Framework_MockObject_MockObject|IQueueProvider */
	private $queue;
	
	/** @var \PHPUnit_Framework_MockObject_MockObject|IMainQueue */
	private $mainQueue;
	
	/** @var ProcessTarget */
	private $target;
	
	
	private function mockSubscribers(array $subscribers)
	{
		$mock = $this->getMockBuilder(ISubscribersModule::class)->getMock();
		$mock->method('get')->willReturn($subscribers);
		$this->subModule = $mock;
		
		\UnitTestScope::override(ISubscribersModule::class, $this->subModule);
	}
	
	private function mockQueue(): IQueueProvider
	{
		$mock = $this->getMockBuilder(IQueue::class)->getMock();
		
		/** @var IQueueProvider|\PHPUnit_Framework_MockObject_MockObject $providerMock */
		$providerMock = $this->getMockBuilder(IQueueProvider::class)->getMock();
		$providerMock->method('getQueue')->willReturn($mock);
		$this->queue = $mock;
		
		return $providerMock;
	}

	/**
	 * @return \PHPUnit_Framework_MockObject_MockObject|IMainQueue
	 */
	private function mockMainQueue(): IMainQueue
	{
		if (!$this->mainQueue)
		{
			$this->mainQueue = $this->getMockBuilder(IMainQueue::class)->getMock();
			\UnitTestScope::override(IMainQueue::class, $this->mainQueue);
		}
		
		return $this->mainQueue;
	}
	
	private function subject($config = null): EventPayload
	{
		if (!$config)
		{
			$config = new Config();
			$config->Engine->QueueProvider = $this->mockQueue();
		}
		
		$this->mockMainQueue();
		
		return \UnitTestScope::load(EventPayload::class, [IConfig::class => $config]);
	}
	
	
	protected function setUp()
	{
		$this->target = new ProcessTarget();
		$this->target->Object = new EventObject();
		$this->target->Object->Name = 'abc';
		$this->target->Payload = [['a'], ['b']];
		
		\UnitTestScope::clear();
	}
	
	
	public function test_NoSubscribers_NoExceptionThrown()
	{
		$this->mockSubscribers([]);
		$this->subject()->process($this->target);
	}
	
	
	public function test_SubscriberIsNotActive_SubscriberNotCalled()
	{
		$handler = new HandlerObject();
		$handler->Name = 'a';
		$handler->State = EventState::DELETED;
		$handler->HandlerClassName = EventPayload_TestHander::class;
				
		$this->mockSubscribers([$handler]);
		
		$subject = $this->subject();
		$this->queue->expects($this->never())->method('enqueue');
		
		
		$subject->process($this->target);
	}
	
	public function test_SubscriberIsPaused_SubscriberCalled()
	{
		$handler = new HandlerObject();
		$handler->Name = 'a';
		$handler->State = EventState::PAUSED;
		$handler->HandlerClassName = EventPayload_TestHander::class;
		
		$this->mockSubscribers([$handler]);
		
		$subject = $this->subject();
		$this->queue->expects($this->once())->method('enqueue')->with($this->target->Payload, $this->anything());
		
		
		$subject->process($this->target);
	}
	
	public function test_SubscriberIsRunning_SubscriberCalled()
	{
		$handler = new HandlerObject();
		$handler->Name = 'a';
		$handler->State = EventState::RUNNING;
		$handler->HandlerClassName = EventPayload_TestHander::class;
		
		$this->mockSubscribers([$handler]);
		
		$subject = $this->subject();
		$this->queue->expects($this->once())->method('enqueue')->with($this->target->Payload, $this->anything());
		
		
		$subject->process($this->target);
	}
	
	
	public function test_SubscribersDelayPassed_SubscriberCalled()
	{
		$handler = new HandlerObject();
		$handler->Name = 'a';
		$handler->Delay = 123;
		$handler->HandlerClassName = EventPayload_TestHander::class;
		
		$this->mockSubscribers([$handler]);
		
		$subject = $this->subject();
		$this->queue->expects($this->once())->method('enqueue')->with($this->anything(), 123);
		
		
		$subject->process($this->target);
	}
	
	
	public function test_NumberOfSubscribers_AllSubscribersUsed()
	{
		$handler1 = new HandlerObject();
		$handler1->Name = 'a';
		$handler1->HandlerClassName = EventPayload_TestHander::class;
		
		$handler2 = new HandlerObject();
		$handler2->Name = 'a';
		$handler2->HandlerClassName = EventPayload_TestHander::class;
		
		$this->mockSubscribers([$handler1, $handler2]);
		
		$subject = $this->subject();
		$this->queue->expects($this->exactly(2))->method('enqueue');
		
		$subject->process($this->target);
	}
	
	
	public function test_OneSubscriber_SubscriberScheduledInMainQueue()
	{
		$handler1 = new HandlerObject();
		$handler1->Name = 'a';
		$handler1->HandlerClassName = EventPayload_TestHander::class;
		
		$main = $this->mockMainQueue();
		$this->mockSubscribers([$handler1]);
		
		$main->expects($this->once())->method('schedule')->with($handler1->getQueueNaming(new Naming()));
		
		$subject = $this->subject();
		$subject->process($this->target);
	}
	
	public function test_NumberOfSubscribers_AllSubscribersScheduledInTheMainQueue()
	{
		$handler1 = new HandlerObject();
		$handler1->Name = 'a';
		$handler1->HandlerClassName = EventPayload_TestHander::class;
		
		$handler2 = new HandlerObject();
		$handler2->Name = 'b';
		$handler2->HandlerClassName = EventPayload_TestHander::class;
		
		
		$main = $this->mockMainQueue();
		$this->mockSubscribers([$handler1, $handler2]);
		
		$main->expects($this->at(0))->method('schedule')->with($handler1->getQueueNaming(new Naming()));
		$main->expects($this->at(1))->method('schedule')->with($handler2->getQueueNaming(new Naming()));
		
		$subject = $this->subject();
		$subject->process($this->target);
	}
	
	
	public function test_QueueAcquiredByHandlersName()
	{
		$handler = new HandlerObject();
		$handler->Name = 'h_a';
		$handler->HandlerClassName = EventPayload_TestHander::class;
		
		$this->mockSubscribers([$handler]);
		
		$config = new Config();
		$mock = $this->getMockBuilder(IQueue::class)->getMock();
		
		/** @var IQueueProvider|\PHPUnit_Framework_MockObject_MockObject $providerMock */
		$providerMock = $this->getMockBuilder(IQueueProvider::class)->getMock();
		$config->Engine->QueueProvider = $providerMock;
			
		
		$subject = $this->subject($config);
		$providerMock
			->expects($this->once())
			->method('getQueue')
			->with($handler->getQueueNaming($config->Naming))
			->willReturn($mock);
		
		
		$subject->process($this->target);
	}
	
	public function test_AllPayloadsFilteredByHandler_NothingProcessed()
	{
		$class = new class extends AbstractHandlerConfig implements IHandlerConfig
		{
			public function filter($item): bool
			{
				return false;
			}
		};
		
		$handler = new HandlerObject();
		$handler->Name = 'a';
		$handler->State = EventState::RUNNING;
		$handler->HandlerClassName = get_class($class);
				
		$this->mockSubscribers([$handler]);
		
		$subject = $this->subject();
		$this->queue->expects($this->never())->method('enqueue');
	
		$subject->process($this->target);
	}
	
	public function test_SomePayloadFilteredByHandler_OtherProcessed()
	{
		$class = new class extends AbstractHandlerConfig implements IHandlerConfig
		{
			public function filter($item): bool
			{
				return $item === ['a'];
			}
		};
		
		$handler = new HandlerObject();
		$handler->Name = 'a';
		$handler->State = EventState::RUNNING;
		$handler->HandlerClassName = get_class($class);
				
		$this->mockSubscribers([$handler]);
		
		$subject = $this->subject();
				
		$this->queue->expects($this->once())->method('enqueue')->with([['a']], $this->anything());
	
		$subject->process($this->target);
	}
	
	public function test_PayloadConvertedAndPayloadFiltered_KeysKeeped_EnqueueCalled()
	{
		$this->target->Payload = ['_a' => 'a', '_b' => 'b'];
		
		$class = new class extends AbstractHandlerConfig implements IHandlerConfig
		{
			public function filter($item): bool
			{
				return $item === 'b';
			}
			
			public function convert($item)
			{
				if ($item == 'b')
					return 'c';
				
				return $item;
			}
		};
		
		$handler = new HandlerObject();
		$handler->Name = 'a';
		$handler->State = EventState::RUNNING;
		$handler->ConfigClassName = get_class($class);
				
		$this->mockSubscribers([$handler]);
		
		$subject = $this->subject();
				
		$this->queue->expects($this->once())->method('enqueue')->with(['_b' => 'c'], $this->anything());
	
		$subject->process($this->target);
	}
}


class EventPayload_TestHander extends AbstractHandlerConfig implements IHandlerConfig {}