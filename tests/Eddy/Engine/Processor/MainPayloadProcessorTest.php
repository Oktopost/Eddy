<?php
namespace Eddy\Engine\Processor;


use Eddy\Base\Engine\Processor\IPayloadProcessorFactory;
use Eddy\Base\Engine\Processor\IProcessControlChain;
use Eddy\Base\Engine\Processor\ProcessTarget;
use Eddy\Objects\EventObject;
use Eddy\Objects\HandlerObject;
use Eddy\Scope;
use Eddy\Base\IConfig;
use Eddy\Base\Engine\Processor\IPayloadProcessor;
use Eddy\Utils\Config;

use PHPUnit\Framework\TestCase;
use PHPUnit\Runner\Exception;


class MainPayloadProcessorTest extends TestCase
{
	/**
	 * @return \PHPUnit_Framework_MockObject_MockObject|IProcessControlChain
	 */
	private function mockChain()
	{
		$chain = $this->getMockBuilder(IProcessControlChain::class)->getMock();
		\UnitTestScope::override(IProcessControlChain::class, $chain);
		return $chain;
	}
	
	private function mockProcessor()
	{
		return $this->getMockBuilder(IPayloadProcessor::class)->getMock();
	}
	
	private function mockFactory(IPayloadProcessor $item = null)
	{
		if (!$item)
			$item = $this->mockProcessor();
		
		$factory = $this->getMockBuilder(IPayloadProcessorFactory::class)->getMock();
		\UnitTestScope::override(IPayloadProcessorFactory::class, $factory);
		$factory->method('get')->willReturn($item);
	}

	private function subject(): MainPayloadProcessor
	{
		return Scope::skeleton()->for([IConfig::class => new Config()])->load(MainPayloadProcessor::class);
	}
	
	private function target(): ProcessTarget
	{
		$target = new ProcessTarget();
		$target->Payload = [['a']];
		$target->Object = new HandlerObject();
		return $target;
	}
	
	
	protected function setUp()
	{
		\UnitTestScope::clear();
	}
	
	
	public function test_sanity_SkeletonSetup()
	{
		$result = Scope::skeleton()->for([IConfig::class => new Config()])->get(IPayloadProcessor::class);
		self::assertInstanceOf(MainPayloadProcessor::class, $result);
	}
	
	
	public function test_process_preProcessInvoked()
	{
		$chain = $this->mockChain();
		$this->mockFactory();
		
		$subject = $this->subject();
		$target = $this->target();
		
		
		$chain->expects($this->once())->method('preProcess')->with($target->Object, $target->Payload);
		
		
		$subject->process($target);
	}
	
	public function test_process_postProcessInvoked()
	{
		$chain = $this->mockChain();
		$this->mockFactory();
		
		$subject = $this->subject();
		$target = $this->target();
		
		
		$chain->expects($this->once())->method('postProcess')->with($target->Object, $target->Payload);
		
		
		$subject->process($target);
	}
	
	public function test_process_TargetPassedToProcessor()
	{
		$this->mockChain();
		$item = $this->mockProcessor();
		$this->mockFactory($item);
		
		$subject = $this->subject();
		$target = $this->target();
		
		
		$item->expects($this->once())->method('process')->with($target);
		
		
		$subject->process($target);
	}
	
	public function test_process_ObjectPassedToFactory()
	{
		$this->mockChain();
		
		$factory = $this->getMockBuilder(IPayloadProcessorFactory::class)->getMock();
		\UnitTestScope::override(IPayloadProcessorFactory::class, $factory);
		
		$subject = $this->subject();
		$target = $this->target();
		
		
		$factory->expects($this->once())->method('get')->willReturn($target->Object)->willReturn($this->mockProcessor());
		
		
		$subject->process($target);
	}
	
	public function test_process_ExceptionThrown_exceptionOnChainCalled()
	{
		$chain = $this->mockChain();
		$item = $this->mockProcessor();
		$this->mockFactory($item);
		
		$subject = $this->subject();
		$target = $this->target();
		$e = new Exception();
		
		
		$item->method('process')->willThrowException($e);
		$chain->expects($this->once())->method('exception')->with($target->Object, $target->Payload, $e);
		
		
		$subject->process($target);
	}
	
	public function test_process_ExceptionThrown_PostProcessNotCalled()
	{
		$chain = $this->mockChain();
		$item = $this->mockProcessor();
		$this->mockFactory($item);
		
		$subject = $this->subject();
		$target = $this->target();
		$e = new Exception();
		
		
		$item->method('process')->willThrowException($e);
		$chain->expects($this->never())->method('postProcess');
		
		
		$subject->process($target);
	}

	/**
	 * @expectedException \Exception
	 * @expectedExceptionMessage test-exception
	 */
	public function test_process_ExceptionThrown_ObjectIsEventObject_ExceptionRethrown()
	{
		$chain = $this->mockChain();
		$item = $this->mockProcessor();
		$this->mockFactory($item);
		
		$subject = $this->subject();
		$target = $this->target();
		$target->Object = new EventObject();
		
		
		$e = new Exception('test-exception');
		$item->method('process')->willThrowException($e);
		
		
		$subject->process($target);
	}
}