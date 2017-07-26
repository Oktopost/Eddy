<?php
namespace Eddy\Engine\Proxy;


use Eddy\Base\Engine\Publish\IPublisher;
use PHPUnit\Framework\TestCase;


class AbstractProxyTest extends TestCase
{
	public function test_setPublisher()
	{
		/** @var IPublisher $publisher */
		$publisher = $this->getMockBuilder(IPublisher::class)->getMock();
		$subject = new AbstractProxyTestHelper();
		
		$subject->setPublisher($publisher);
		
		self::assertEquals($publisher, $subject->invokePublisher());
	}
	
	public function test_publish()
	{
		/** @var IPublisher|\PHPUnit_Framework_MockObject_MockObject $publisher */
		$publisher = $this->getMockBuilder(IPublisher::class)->getMock();
		$subject = new AbstractProxyTestHelper();
		
		$publisher->expects($this->once())->method('publish')->with(['a']);
		
		$subject->setPublisher($publisher);
		$subject->invokePublish(['a']);
	}
}


class AbstractProxyTestHelper extends AbstractProxy
{
	public function invokePublisher(): IPublisher
	{
		return $this->publisher();
	}
	
	public function invokePublish(array $data)
	{
		$this->publish($data);
	}
}