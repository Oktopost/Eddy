<?php
namespace Eddy\Engine\Queue\DeepQueue;


use DeepQueue\Base\IDeepQueueConfig;
use DeepQueue\Base\IMetaData;
use DeepQueue\Base\IQueueConfig;
use DeepQueue\Base\IQueueObject;
use DeepQueue\Base\Plugins\ConnectorElements\IQueueManager as IDeepQueueQueueManager;
use DeepQueue\Base\Queue\IQueue;
use DeepQueue\DeepQueue;


use Eddy\Base\Engine\Queue\IQueueManager;
use PHPUnit\Framework\TestCase;


class DeepQueueManagerAdapterTest extends TestCase
{
	/**
	 * @return \PHPUnit_Framework_MockObject_MockObject| IDeepQueueQueueManager
	 */
	private function mockQueueManager(): IDeepQueueQueueManager
	{
		return $this->getMockBuilder(IDeepQueueQueueManager::class)->getMock();
	}
	
	
	private function getSubject(IDeepQueueQueueManager $queueManager): IQueueManager
	{
		$dq = new Test_Mananger_DQ($queueManager);
		
		return new DeepQueueManagerAdapter($dq, 'test');
	}
	
	
	public function test_clear_DeepQueueMethodCalled()
	{
		$manager = $this->mockQueueManager();
		$manager->expects($this->once())
			->method('clearQueue');
		
		$this->getSubject($manager)->clear();
	}
	
	public function test_getNextRuntime_DeepQueueMethoCalled()
	{
		$manager = $this->mockQueueManager();
		$manager->expects($this->once())
			->method('getWaitingTime')
			->willReturn(1);
		
		self::assertEquals(1, $this->getSubject($manager)->getNextRuntime());
	}
}


class Test_Mananger_DQ extends DeepQueue
{
	private $queueManager;
	
	
	public function __construct(IDeepQueueQueueManager $queueManager)
	{
		$this->queueManager = $queueManager;
		
		parent::__construct();
	}
	
	public function manager(string $name): IDeepQueueQueueManager
	{
		return $this->queueManager;
	}
	
	public function getQueueObject(string $name): ?IQueueObject
	{
		$mockQueue = new class implements IQueueObject
		{		
			public $Config;
			
			
			public function __construct()
			{
				$class = new class implements IQueueConfig {
					public $DelayBuffer = 0;
					public $MaxBulkSize = 0;
				};
				$this->Config = new $class;
			}

			public function getStream(): IQueue	{}
			public function getMetaData(): IMetaData {}
			public function setDeepConfig(IDeepQueueConfig $config): void {}
		};
		
		return $mockQueue;
	}
}