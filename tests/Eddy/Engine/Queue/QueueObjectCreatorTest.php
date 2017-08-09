<?php
namespace Eddy\Engine\Queue;


use Eddy\Scope;
use Eddy\Base\IConfig;
use Eddy\Base\IEddyQueueObject;
use Eddy\Base\Engine\Queue\IQueueProvider;
use Eddy\Base\Engine\Queue\IQueueObjectCreator;
use Eddy\Base\Engine\Queue\IQueueObjectManager;
use Eddy\Object\EventObject;
use Eddy\Utils\Config;
use Eddy\Utils\Naming;

use PHPUnit\Framework\TestCase;


class QueueObjectCreatorTest extends TestCase
{
	private function getSubject(): IQueueObjectCreator
	{
		$obj = new \stdClass();
		Scope::skeleton()->context($obj, 'test')->set(IConfig::class, $this->createConfig());
		
		return Scope::skeleton($obj, IQueueObjectCreator::class);
	}
	
	
	private function createConfig(): IConfig
	{
		$config = new Config();
		$config->Naming = $this->getMockBuilder(Naming::class)->getMock();
		$config->Naming->method('__get')
			->with($this->anything())
			->willReturn('testName');
		
		$config->Engine->QueueProvider = $this->getIQueueProviderMock();
		
		return $config;
	}

	private function getIQueueProviderMock()
	{
		$queueProvider = $this->getMockBuilder(IQueueProvider::class)->getMock();
		
		$managerMock = $this->getMockBuilder(IQueueObjectManager::class)->getMock();
		$managerMock->expects($this->atLeastOnce())
			->method('save');
		
		$managerMock->expects($this->atLeastOnce())
			->method('load');
		
		$queueProvider->expects($this->atLeastOnce())
			->method('getObjectManager')
			->willReturn($managerMock);
		
		return $queueProvider;
	}
	
	private function getEvent(): IEddyQueueObject
	{
		$event = new EventObject();
		$event->Name = 'Event';
		$event->Delay = 1;
		
		return $event;
	}
	
	
	protected function setUp()
	{
		\UnitTestScope::clear();
	}
	
	
	public function test_createQueue()
	{
		$this->getSubject()->createQueue($this->getEvent());
	}
	
	public function test_createQueues()
	{
		$this->getSubject()->createQueues([$this->getEvent()]);
	}
}