<?php
namespace Eddy\Engine\Processor;


use Eddy\Base\Engine\Processor\IIterationProcessor;
use Eddy\Base\Engine\Processor\IProcessControlChain;
use Eddy\Base\IExceptionHandler;
use Eddy\Exceptions\AbortException;
use Eddy\Scope;
use Eddy\Base\IConfig;
use Eddy\Base\Engine\IProcessor;
use Eddy\Utils\Config;

use PHPUnit\Framework\TestCase;


class MainProcessorTest extends TestCase
{
	/** @var IIterationProcessor|\PHPUnit_Framework_MockObject_MockObject */
	private $iteration;
	
	/** @var IProcessControlChain|\PHPUnit_Framework_MockObject_MockObject */
	private $chain;
	
	
	private function mockIterationProcessor()
	{
		$this->iteration = $this->getMockBuilder(IIterationProcessor::class)->getMock();
		\UnitTestScope::override(IIterationProcessor::class, $this->iteration);
	}
	
	private function mockChain()
	{
		$this->chain = $this->getMockBuilder(IProcessControlChain::class)->getMock();
		\UnitTestScope::override(IProcessControlChain::class, $this->chain);
	}
	
	private function subject($config = null): MainProcessor
	{
		return \UnitTestScope::load(MainProcessor::class, [ IConfig::class => ($config ?: new Config()) ]);
	}
	
	
	public function test_sanity_SkeletonSetup()
	{
		$result = Scope::skeleton()->for([IConfig::class => new Config()])->get(IProcessor::class);
		self::assertInstanceOf(MainProcessor::class, $result);
	}
	
	
	public function test_run_ChainInvoked()
	{
		$this->mockIterationProcessor();
		$this->mockChain();
		
		$subject = $this->subject();
		
		$this->iteration->method('runOnce')->willReturn(false);
		$this->chain
			->expects($this->once())
			->method('init');
		
		$this->chain
			->expects($this->once())
			->method('stopping');
		
		$subject->run();
	}
	
	public function test_run_IteratorCalled()
	{
		$this->mockIterationProcessor();
		$this->mockChain();
		
		$subject = $this->subject();
		
		$this->iteration->expects($this->once())->method('runOnce')->willReturn(false);
		
		$subject->run();
	}
	
	public function test_run_IteratorCalledUntilFalseIsReturned()
	{
		$this->mockIterationProcessor();
		$this->mockChain();
		
		$subject = $this->subject();
		
		$this->iteration
			->expects($this->exactly(3))
			->method('runOnce')
			->willReturnOnConsecutiveCalls(true, true, false);
		
		$subject->run();
	}
	
	public function test_run_AbortExceptionThrown_LoopAborted()
	{
		$this->mockIterationProcessor();
		$this->mockChain();
		
		$subject = $this->subject();
		
		$this->iteration
			->method('runOnce')
			->willThrowException(new AbortException());
		
		$subject->run();
	}
	
	public function test_run_ExceptionThrown_ExceptionHandled()
	{
		$this->mockIterationProcessor();
		$this->mockChain();
		
		$e = new \Exception();
		$config = new Config();
		$subject = $this->subject($config);
		
		$config->ExceptionHandler = $this->getMockBuilder(IExceptionHandler::class)->getMock();
		$config->ExceptionHandler->expects($this->once())
			->method('exception')
			->with($e);
		
		$this->iteration
			->expects($this->at(0))
			->method('runOnce')
			->willThrowException($e);
		
		$this->iteration
			->expects($this->at(1))
			->method('runOnce')
			->willReturn(false);
		
		$subject->run();
	}
}