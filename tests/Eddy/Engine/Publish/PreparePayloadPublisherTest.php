<?php
namespace Eddy\Engine\Publish;


use Eddy\Base\Engine\Publish\IPublisher;
use Eddy\IEventConfig;
use PHPUnit\Framework\TestCase;


class PreparePayloadPublisherTest extends TestCase
{
	/** @var \PHPUnit_Framework_MockObject_MockObject|IPublisher */
	private $publisher;
	
	/** @var PayloadPreprocessorPublisherTestConfig */
	private $config;
	
	
	private function subject(): PreparePayloadPublisher
	{
		$this->publisher = $this->getMockBuilder(IPublisher::class)->getMock();
		$this->config = new PayloadPreprocessorPublisherTestConfig();
		
		$subject = new PreparePayloadPublisher($this->publisher);
		$subject->setEventConfig($this->config);
		
		return $subject;
	}
	
	
	public function test_publish_EmptyData_PrepareNotCalled()
	{
		$subject = $this->subject();
		
		$subject->publish([]);
		
		self::assertNull($this->config->calledWith);
	}
	
	public function test_publish_HaveData_DataPassedToPrepare()
	{
		$subject = $this->subject();
		
		$subject->publish(['a']);
		
		self::assertEquals(['a'], $this->config->calledWith);
	}
	
	public function test_publish_ReturnNullFromPrepare_OriginalDataPublished()
	{
		$subject = $this->subject();
		$this->config->return = null;
		
		$this->publisher->expects($this->once())->method('publish')->with(['a']);
		
		$subject->publish(['a']);
	}
	
	public function test_publish_ReturnFalseValueFromPrepare_NoDataPublished()
	{
		$subject = $this->subject();
		$this->config->return = [];
		
		$this->publisher->expects($this->never())->method('publish');
		
		$subject->publish(['a']);
	}
	
	public function test_publish_ReturnDifferentDataFromArray_ReturnedDataPublished()
	{
		$subject = $this->subject();
		$this->config->return = ['b' => 'c'];
		
		$this->publisher->expects($this->once())->method('publish')->with(['b' => 'c']);
		
		$subject->publish(['a']);
	}
}


class PayloadPreprocessorPublisherTestConfig implements IEventConfig
{
	public $calledWith = null;
	public $return = [];
	
	public function name(): string {}
	public function delay(): ?float {}
	public function initialState(): string {}
	public function eventClassName(): string {}
	public function proxyClassName(): ?string {}
	public function handlersInterface(): string {}
	public function maxBulkSize(): int {}

	public function prepare(array $data): ?array
	{
		$this->calledWith = $data;
		return $this->return;
	}

}