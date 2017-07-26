<?php
namespace Eddy\Engine\Publish;


use Eddy\Base\Engine\Publish\IPublisher;
use PHPUnit\Framework\TestCase;


class PublishersCollectionTest extends TestCase
{
	public function test_publish_EmptyCollection_NoExceptions()
	{
		$subject = new PublishersCollection();
		$subject->publish(['a']);
	}
	
	
	public function test_publish_SinglePublisherRegistered_PublisherInvoked()
	{
		$publisher = $this->getMockBuilder(IPublisher::class)->getMock();
		$subject = new PublishersCollection();
		
		$subject->add($publisher);
		
		
		$publisher->expects($this->once())->method('publish')->with(['a']);
		
		
		$subject->publish(['a']);
	}
	
	public function test_publish_NumberOfPublishersRegistered_PublishersInvoked()
	{
		$publisher1 = $this->getMockBuilder(IPublisher::class)->getMock();
		$publisher2 = $this->getMockBuilder(IPublisher::class)->getMock();
		$subject = new PublishersCollection();
		
		$subject->add($publisher1, $publisher2);
		
		
		$publisher1->expects($this->once())->method('publish')->with(['a']);
		$publisher2->expects($this->once())->method('publish')->with(['a']);
		
		
		$subject->publish(['a']);
	}
	
	public function test_publish_NumberOfPublishersRegisteredAsArray_PublishersInvoked()
	{
		$publisher1 = $this->getMockBuilder(IPublisher::class)->getMock();
		$publisher2 = $this->getMockBuilder(IPublisher::class)->getMock();
		$subject = new PublishersCollection();
		
		$subject->add([$publisher1, $publisher2]);
		
		
		$publisher1->expects($this->once())->method('publish')->with(['a']);
		$publisher2->expects($this->once())->method('publish')->with(['a']);
		
		
		$subject->publish(['a']);
	}
	
	public function test_publish_NumberOfPublishersRegisteredInSequence_PublishersInvoked()
	{
		$publisher1 = $this->getMockBuilder(IPublisher::class)->getMock();
		$publisher2 = $this->getMockBuilder(IPublisher::class)->getMock();
		$subject = new PublishersCollection();
		
		$subject->add($publisher1);
		$subject->add($publisher2);
		
		
		$publisher1->expects($this->once())->method('publish')->with(['a']);
		$publisher2->expects($this->once())->method('publish')->with(['a']);
		
		
		$subject->publish(['a']);
	}
	
	public function test_publish_NumberOfPublishersRegistered_OrderIsCorrect()
	{
		$publisher1 = $this->getMockBuilder(IPublisher::class)->getMock();
		$publisher2 = $this->getMockBuilder(IPublisher::class)->getMock();
		$subject = new PublishersCollection();
		$order = [];
		
		$subject->add([$publisher1, $publisher2]);
		
		$publisher1->method('publish')->willReturnCallback(function() use (&$order, $publisher1) { $order[] = $publisher1; });
		$publisher2->method('publish')->willReturnCallback(function() use (&$order, $publisher2) { $order[] = $publisher2; });
		
		$subject->publish(['a']);
		
		
		self::assertEquals([$publisher1, $publisher2], $order);
	}
}