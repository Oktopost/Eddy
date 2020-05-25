<?php
namespace Eddy\Engine\Processor;


use Eddy\Base\Engine\Processor\IIterationProcessor;
use Eddy\Base\Engine\Processor\IPayloadLoader;
use Eddy\Base\Engine\Processor\ProcessTarget;
use Eddy\Base\IConfig;
use Eddy\Base\Engine\IMainQueue;
use Eddy\Base\Engine\ILockProvider;
use Eddy\Base\Engine\Lock\ILocker;
use Eddy\Base\Engine\Processor\IPayloadProcessor;
use Eddy\Base\Engine\Processor\IProcessControlChain;
use Eddy\Objects\EventObject;
use Eddy\Scope;
use Eddy\Utils\Config;

use PHPUnit\Framework\TestCase;


class IterationProcessorTest extends TestCase
{
	/** @var IProcessControlChain|\PHPUnit_Framework_MockObject_MockObject */
	private $chain;
	
	/** @var IMainQueue|\PHPUnit_Framework_MockObject_MockObject */
	private $main;

	/** @var IConfig */
	private $config;

	/** @var IPayloadProcessor|\PHPUnit_Framework_MockObject_MockObject */
	private $payloadProcessor;

	/** IPayloadLoader|\PHPUnit_Framework_MockObject_MockObject */
	private $payloadLoader;
	
	/** @var ILockProvider|\PHPUnit_Framework_MockObject_MockObject */
	private $lockProvider;
	
	/** @var ILocker|\PHPUnit_Framework_MockObject_MockObject */
	private $locker;
	
	
	private function mockLocker()
	{
		if ($this->locker) return;
		
		$this->locker = $this->getMockBuilder(ILocker::class)->getMock();
		$this->locker->method('lock')->willReturn(true);
	}
	
	private function mockLockProvider()
	{
		if (!$this->lockProvider)
		{
			$this->mockLocker();
			
			$this->lockProvider = $this->getMockBuilder(ILockProvider::class)->getMock();
			$this->lockProvider->method('get')->willReturn($this->locker);
		}
		
		$this->config->Engine->Locker = $this->lockProvider;
	}
	
	private function mockPayloadLoader($result = true)
	{
		if (!$this->payloadLoader)
		{
			if ($result === true || is_string($result))
			{
				$name = ($result === true ? 'abc' : $result);
				
				$result = new ProcessTarget();
				$result->Object = new EventObject();
				$result->Object->Name = $name;
				$result->Payload = ['a', 'b'];
			}
			
			$this->payloadLoader = $this->getMockBuilder(IPayloadLoader::class)->getMock();
			$this->payloadLoader->method('getPayloadFor')->willReturn($result);
		}
		
		\UnitTestScope::override(IPayloadLoader::class, $this->payloadLoader);
	}
	
	private function mockMainQueue($result = 'abc')
	{
		if (!$this->main)
		{
			$this->main = $this->getMockBuilder(IMainQueue::class)->getMock();
			$this->main->method('dequeue')->willReturn($result); 
		}
		
		\UnitTestScope::override(IMainQueue::class, $this->main);
	}
	
	private function mockChain()
	{
		if (!$this->chain)
		{
			$this->chain = $this->getMockBuilder(IProcessControlChain::class)->getMock();
			$this->chain->method('start')->willReturn(true); 
			$this->chain->method('waiting')->willReturn(0.0);
		}
		
		\UnitTestScope::override(IProcessControlChain::class, $this->chain);
	}
	
	private function mockPayloadProcessor()
	{
		if (!$this->payloadProcessor)
		{
			$this->payloadProcessor = $this->getMockBuilder(IPayloadProcessor::class)->getMock();
		}
		
		\UnitTestScope::override(IPayloadProcessor::class, $this->payloadProcessor);
	}
	
	private function mock()
	{
		$this->mockLockProvider();
		$this->mockChain();
		$this->mockMainQueue();
		$this->mockPayloadLoader();
		$this->mockPayloadProcessor();
	}
	
	
	private function subject(): IterationProcessor
	{
		$this->mock();
		return Scope::skeleton()->for([IConfig::class => $this->config])->load(IterationProcessor::class);
	}
	
	private function runSubject($expect = true)
	{
		self::assertEquals($expect, $this->subject()->runOnce());
	}
	
	
	protected function setUp()
	{
		\UnitTestScope::clear();
		$this->config = new Config();
	}
	
	protected function tearDown()
	{
		\UnitTestScope::clear();
	}
	
	
	public function test_Sanity_SkeletonSet()
	{
		$subject = Scope::skeleton()->for([IConfig::class => new Config()])->get(IIterationProcessor::class);
		self::assertInstanceOf(IterationProcessor::class, $subject);
	}
	
	
	public function test_runOnce_ChainStartReturnFalse_ReturnFalse()
	{
		$this->chain = $this->getMockBuilder(IProcessControlChain::class)->getMock();
		$this->chain->method('start')->willReturn(false);
		
		$this->runSubject(false);
	}
	
	public function test_runOnce_ChainStartReturnFalse_MainQueueNeverInvoked()
	{
		$this->chain = $this->getMockBuilder(IProcessControlChain::class)->getMock();
		$this->chain->method('start')->willReturn(false);
		
		$this->main = $this->getMockBuilder(IMainQueue::class)->getMock();
		$this->main->expects($this->never())->method('dequeue'); 
		
		
		$this->runSubject(false);
	}
	
	
	public function test_runOnce_NoQueueToProcess_ProcessorNotInvoked()
	{
		$this->mockPayloadLoader(null);
		$this->mock();
		
		$this->payloadProcessor->expects($this->never())->method('process');
		
		
		$this->runSubject();
	}
	
	public function test_runOnce_WaitTimeSetToZero_TryGetTargetCalledOnlyOnce()
	{
		$this->mockPayloadLoader(null);
		
		$this->chain = $this->getMockBuilder(IProcessControlChain::class)->getMock();
		$this->chain->method('start')->willReturn(true); 
		$this->chain->method('waiting')->willReturn(0.0);
		
		$this->main = $this->getMockBuilder(IMainQueue::class)->getMock();
		$this->main->expects($this->once())->method('dequeue')->with(0.0)->willReturn(null); 
		
		
		$this->runSubject();
	}
	
	
	public function test_runOnce_WaitTimeGreaterThenZero_DequeueCalledTwice()
	{
		$this->chain = $this->getMockBuilder(IProcessControlChain::class)->getMock();
		$this->chain->method('start')->willReturn(true); 
		$this->chain->method('waiting')->willReturn(10.0);
		
		$this->main = $this->getMockBuilder(IMainQueue::class)->getMock();
		
		$this->main->expects($this->at(0))->method('dequeue')->with(0.0)->willReturn(null);
		$this->main->expects($this->at(1))->method('dequeue')->willReturnCallback(
			function ($time)
			{
				self::assertEquals(10.0, $time, '', 0.1);
				return 'abc';
			}
		);
		
		
		$this->runSubject();
	}
	
	
	public function test_runOnce_QueueFound_QueueLocked()
	{
		$data = [];
		$this->mockMainQueue('abc');
		
		$this->locker = $this->getMockBuilder(ILocker::class)->getMock();
		$this->locker->expects($this->at(0))->method('lock')->willReturnCallback(
			function() 
				use (&$data) 
			{
				$data[] = 'lock';
				return true;
			});
		
		$this->lockProvider = $this->getMockBuilder(ILockProvider::class)->getMock();
		$this->lockProvider->method('get')->with('abc')->willReturn($this->locker);
		
		$this->payloadLoader = $this->getMockBuilder(IPayloadLoader::class)->getMock();
		$this->payloadLoader->method('getPayloadFor')->willReturnCallback(
			function()
				use (&$data)
			{
				$data[] = 'get';
				return null;
			});
		
		
		$this->runSubject();
		
		
		self::assertEquals(['lock', 'get'], $data);
	}
	
	
	public function test_runOnce_LockOnQueueFailed_NoDataRequested()
	{
		$this->mockMainQueue('abc');
		
		$this->locker = $this->getMockBuilder(ILocker::class)->getMock();
		$this->locker->expects($this->once())->method('lock')->willReturn(false);
		
		$this->payloadLoader = $this->getMockBuilder(IPayloadLoader::class)->getMock();
		$this->payloadLoader->expects($this->never())->method('getPayloadFor');
		
		
		$this->runSubject();
	}


	/**
	 * @expectedException \Exception
	 * @expectedExceptionMessage test-exception
	 */
	public function test_runOnce_ExceptionOnLoadingPayload_QueueRescheduledAndReleased()
	{
		$this->locker = $this->getMockBuilder(ILocker::class)->getMock();
		$this->locker->expects($this->once())->method('lock')->willReturn(true);
		$this->locker->expects($this->once())->method('unlock');
		
		
		$this->payloadLoader = $this->getMockBuilder(IPayloadLoader::class)->getMock();
		$this->payloadLoader->method('getPayloadFor')->willThrowException(new \Exception('test-exception'));
		
		$this->main = $this->getMockBuilder(IMainQueue::class)->getMock();
		
		$this->main->method('dequeue')->willReturn('abc');
		$this->main->expects($this->once())->method('schedule')->with('abc');
		
		
		$this->runSubject();
	}

	/**
	 * @expectedException \Exception
	 * @expectedExceptionMessage test-exception
	 */
	public function test_runOnce_ExceptionInProcessor_QueueRescheduledAndReleased()
	{
		$this->mockPayloadLoader('abc');
		
		$this->payloadProcessor = $this->getMockBuilder(IPayloadProcessor::class)->getMock();
		$this->payloadProcessor->method('process')->willThrowException(new \Exception('test-exception'));
		
		$this->locker = $this->getMockBuilder(ILocker::class)->getMock();
		$this->locker->expects($this->once())->method('lock')->willReturn(true);
		$this->locker->expects($this->once())->method('unlock');
		
		$this->main = $this->getMockBuilder(IMainQueue::class)->getMock();
		$this->main->method('dequeue')->willReturn('Eddy:Event:abc');
		$this->main->expects($this->once())->method('schedule')->with('Eddy:Event:abc');
		
		
		$this->runSubject();
	}
	
	public function test_runOnce_DataProcessed_QueueRescheduledAndReleased()
	{
		$this->mockPayloadLoader('abc');
		
		$this->locker = $this->getMockBuilder(ILocker::class)->getMock();
		$this->locker->expects($this->once())->method('lock')->willReturn(true);
		$this->locker->expects($this->once())->method('unlock');
		
		$this->main = $this->getMockBuilder(IMainQueue::class)->getMock();
		$this->main->method('dequeue')->willReturn('Eddy:Event:abc');
		$this->main->expects($this->once())->method('schedule')->with('Eddy:Event:abc');
		
		
		$this->runSubject();
	}
	
	
	public function test_runOnce_DataLoaded_DataPassedToProcessor()
	{
		$data = new ProcessTarget();
		$data->Object = new EventObject();
		$data->Object->Name = 'abc';
		$data->Payload = ['abc', 'def'];
		
		$this->mockPayloadLoader($data);
		
		$this->payloadProcessor = $this->getMockBuilder(IPayloadProcessor::class)->getMock();
		$this->payloadProcessor->expects($this->once())->method('process')->with($data);
		
		$this->runSubject();
	}
	
	
	public function test_runOnce_FailedToDequeuPayloadForQueue_QueueCalledAgain()
	{
		$data = new ProcessTarget();
		$data->Object = new EventObject();
		$data->Object->Name = 'def';
		$data->Payload = ['abc', 'def'];
		
		$this->chain = $this->getMockBuilder(IProcessControlChain::class)->getMock();
		$this->chain->method('start')->willReturn(true); 
		$this->chain->method('waiting')->willReturn(1.0);
		
		$this->payloadLoader = $this->getMockBuilder(IPayloadLoader::class)->getMock();
		$this->payloadLoader->expects($this->at(0))->method('getPayloadFor')->willReturn(null);
		$this->payloadLoader->expects($this->at(1))->method('getPayloadFor')->willReturn($data);
		
		$this->locker = $this->getMockBuilder(ILocker::class)->getMock();
		$this->locker->expects($this->at(0))->method('lock')->willReturn(true);
		$this->locker->expects($this->at(1))->method('unlock');
		$this->locker->expects($this->at(2))->method('lock')->willReturn(true);
		$this->locker->expects($this->at(3))->method('unlock');
		
		$this->lockProvider = $this->getMockBuilder(ILockProvider::class)->getMock();
		$this->lockProvider->expects($this->at(0))->method('get')->with('Eddy:Event:abc')->willReturn($this->locker);
		$this->lockProvider->expects($this->at(1))->method('get')->with('Eddy:Event:abc')->willReturn($this->locker);
		$this->lockProvider->expects($this->at(2))->method('get')->with('Eddy:Event:def')->willReturn($this->locker);
		$this->lockProvider->expects($this->at(3))->method('get')->with('Eddy:Event:def')->willReturn($this->locker);
		
		$this->main = $this->getMockBuilder(IMainQueue::class)->getMock();
		$this->main->expects($this->at(0))->method('dequeue')->willReturn('Eddy:Event:abc');
		$this->main->expects($this->at(1))->method('dequeue')->willReturn('Eddy:Event:def');
		
		$this->runSubject();
	}
}