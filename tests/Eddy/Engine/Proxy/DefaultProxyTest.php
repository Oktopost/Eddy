<?php
namespace Eddy\Engine\Proxy;


use Eddy\Base\Engine\Publish\IPublisher;
use PHPUnit\Framework\TestCase;


class DefaultProxyTest extends TestCase
{
	/** @var IPublisher|\PHPUnit_Framework_MockObject_MockObject */
	private $publisher;
	
	/** @var DefaultProxy|DefaultProxyTestHelper $subject */
	private $subject;
	
	
	protected function setUp()
	{
		$this->publisher = $this->getMockBuilder(IPublisher::class)->getMock();
		$this->subject = new DefaultProxy(DefaultProxyTestHelper::class);
		$this->subject->setPublisher($this->publisher);
	}
	
	
	/**
	 * @expectedException \Eddy\Exceptions\UnexpectedException
	 */
	public function test_InvokeWithMultipleArguments_ExceptionThrown()
	{
		/** @noinspection PhpMethodParametersCountMismatchInspection */
		$this->subject->methodParam(1, 2);
	}
	
	/**
	 * @expectedException \Eddy\Exceptions\UnexpectedException
	 */
	public function test_NoParametersPassed_ExceptionThrown()
	{
		/** @noinspection PhpParamsInspection */
		$this->subject->methodParam();
	}
	
	/**
	 * @expectedException \Eddy\Exceptions\UnexpectedException
	 */
	public function test_MethodHasNoParameters_ExceptionThrown()
	{
		/** @noinspection PhpMethodParametersCountMismatchInspection */
		$this->subject->methodNoParams(1);
	}
	
	/**
	 * @expectedException \Eddy\Exceptions\UnexpectedException
	 */
	public function test_MethodNotPublic_ExceptionThrown()
	{
		/** @var mixed $subject */
		$subject = $this->subject; 
		$subject->methodNotPublic(1);
	}
	
	/**
	 * @expectedException \Eddy\Exceptions\UnexpectedException
	 */
	public function test_MethodWithoutParams_ExceptionThrown()
	{
		/** @noinspection PhpMethodParametersCountMismatchInspection */
		$this->subject->methodNoParams(1);
	}
	
	/**
	 * @expectedException \Eddy\Exceptions\UnexpectedException
	 */
	public function test_MethodWithoutMultipleParameters_ExceptionThrown()
	{
		/** @noinspection PhpParamsInspection */
		$this->subject->methodParams(1);
	}
	
	/**
	 * @expectedException \Eddy\Exceptions\UnexpectedException
	 */
	public function test_MethodWithArrayParameter_NonArrayValuePassed_ExceptionThrown()
	{
		/** @noinspection PhpParamsInspection */
		$this->subject->methodArrayParam(1);
	}
	
	
	public function test_MethodWithMixedParameter_PublisherCalledWithArray()
	{
		$this->publisher->expects($this->once())->method('publish')->with(['a']);
		$this->subject->methodParam('a');
	}
	
	public function test_MethodWithDefinedPArameterType_PublisherCalledWithArray()
	{
		$this->publisher->expects($this->once())->method('publish')->with(['a']);
		$this->subject->methodIntParam('a');
	}
	
	public function test_MethodWithMixedParameter_PassedArray_ArrayTreatedAsSinglePayload()
	{
		$this->publisher->expects($this->once())->method('publish')->with([['a']]);
		$this->subject->methodParam(['a']);
	}
	
	public function test_MethodWithArrayParameter_SameArrayPassedToPublisher()
	{
		$this->publisher->expects($this->once())->method('publish')->with(['a', ['b']]);
		$this->subject->methodArrayParam(['a', ['b']]);
	}
}


abstract class DefaultProxyTestHelper
{
	public abstract function methodNoParams();
	public abstract function methodParams($param1, $param2);
	protected abstract function methodNotPublic($param);
	
	public abstract function methodParam($param);
	public abstract function methodIntParam(int $param);
	public abstract function methodArrayParam(array $param);
}