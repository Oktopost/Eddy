<?php
namespace Eddy\Engine\Processor;


use Eddy\Base\Engine\IQueue;
use Eddy\Base\Engine\Processor\IPayloadLoader;
use Eddy\Base\Engine\Queue\IQueueProvider;
use Eddy\Base\IConfig;
use Eddy\Base\Module\IEddyObjectModule;
use Eddy\Enums\EventState;
use Eddy\Object\HandlerObject;
use Eddy\Scope;
use Eddy\Utils\Config;
use PHPUnit\Framework\TestCase;


class PayloadLoaderTest extends TestCase
{
	private function mockIEddyObjectModule($object = false)
	{
		$mock = $this->getMockBuilder(IEddyObjectModule::class)->getMock();
		\UnitTestScope::override(IEddyObjectModule::class, $mock);
		
		if ($object != false)
			$mock->method('getByQueueName')->willReturn($object);
		
		return $mock;
	}
	
	private function subject(IConfig &$config = null): IPayloadLoader
	{
		$config = new Config();
		return Scope::skeleton()->for([IConfig::class => $config])->load(PayloadLoader::class);
	}
	
	
	public function test_sanity_SkeletonSetup()
	{
		$config = new Config();
		$subject = Scope::skeleton()->for([IConfig::class => $config])->get(IPayloadLoader::class);
		self::assertInstanceOf(IPayloadLoader::class, $subject);
	}
	
	
	public function test_getPayloadFor_QueueNamePassedToModule()
	{
		$mock = $this->mockIEddyObjectModule();
		$subject = $this->subject();
		
		$mock->expects($this->once())->method('getByQueueName')->with('abc')->willReturn(null);
		
		$subject->getPayloadFor('abc');
	}
	
	
	public function test_getPayloadFor_ObjectNotFoundByModule_ReturnNull()
	{
		$this->mockIEddyObjectModule(null);
		$subject = $this->subject();
		
		self::assertNull($subject->getPayloadFor('abc'));
	}
	
	public function test_getPayloadFor_ObjectNotRunning_ReturnNull()
	{
		$object = new HandlerObject();
		$object->State = EventState::PAUSED;
		
		$this->mockIEddyObjectModule($object);
		$subject = $this->subject();
		
		self::assertNull($subject->getPayloadFor('abc'));
	}
	
	
	public function test_getPayloadFor_ObjectFound_QueueForObjectRequested()
	{
		$object = new HandlerObject();
		$object->State = EventState::RUNNING;
		$object->Name = 'abc';
		$this->mockIEddyObjectModule($object);
		
		/** @var IConfig $config */
		$subject = $this->subject($config);
		
		$provider = $this->getMockBuilder(IQueueProvider::class)->getMock();
		$queue = $this->getMockBuilder(IQueue::class)->getMock();
		$config->Engine->QueueProvider = $provider;
		
		
		$provider->expects($this->once())->method('getQueue')->with('abc')->willReturn($queue);
		
		
		$subject->getPayloadFor('abc');
	}
	
	public function test_getPayloadFor_ObjectFound_MAxBulkSizePassedToQueue()
	{
		$object = new HandlerObject();
		$object->State = EventState::RUNNING;
		$object->Name = 'abc';
		$object->MaxBulkSize = 123;
		$this->mockIEddyObjectModule($object);
		
		/** @var IConfig $config */
		$subject = $this->subject($config);
		
		$provider = $this->getMockBuilder(IQueueProvider::class)->getMock();
		$queue = $this->getMockBuilder(IQueue::class)->getMock();
		$config->Engine->QueueProvider = $provider;
		
		
		$queue->expects($this->once())->method('dequeue')->with(123)->willReturn([]);
		$provider->method('getQueue')->willReturn($queue);
		
		
		$subject->getPayloadFor('abc');
	}
	
	public function test_getPayloadFor_QueueIsEmpty_ReturnNull()
	{
		$object = new HandlerObject();
		$object->State = EventState::RUNNING;
		$object->Name = 'abc';
		$this->mockIEddyObjectModule($object);
		
		/** @var IConfig $config */
		$subject = $this->subject($config);
		
		$provider = $this->getMockBuilder(IQueueProvider::class)->getMock();
		$queue = $this->getMockBuilder(IQueue::class)->getMock();
		$config->Engine->QueueProvider = $provider;
		
		
		$queue->method('dequeue')->willReturn([]);
		$provider->method('getQueue')->willReturn($queue);
		
		
		self::assertNull($subject->getPayloadFor('abc'));
	}
	
	public function test_getPayloadFor_HaveQueueElements_PayloadReturned()
	{
		$object = new HandlerObject();
		$object->State = EventState::RUNNING;
		$object->Name = 'abc';
		$this->mockIEddyObjectModule($object);
		
		/** @var IConfig $config */
		$subject = $this->subject($config);
		
		$provider = $this->getMockBuilder(IQueueProvider::class)->getMock();
		$queue = $this->getMockBuilder(IQueue::class)->getMock();
		$config->Engine->QueueProvider = $provider;
		
		
		$queue->method('dequeue')->willReturn([['a'], ['b']]);
		$provider->method('getQueue')->willReturn($queue);
		
		
		$result = $subject->getPayloadFor('abc');
		
		self::assertNotNull($result);
		self::assertSame($object, $result->Object);
		self::assertEquals([['a'], ['b']], $result->Payload);
	}
}