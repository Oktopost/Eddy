<?php
namespace Eddy\Engine\Queue\DeepQueue;


use Eddy\Base\Engine\Queue\IQueueObjectManager;

use DeepQueue\DeepQueue;
use DeepQueue\Base\IDeepQueueConfig;
use DeepQueue\Base\IQueueObject;
use DeepQueue\Base\Plugins\IManagerPlugin;
use DeepQueue\Manager\QueueObject;

use PHPUnit\Framework\TestCase;


class DeepQueueObjectManagerAdapterTest extends TestCase
{
	private function getSubject(DeepQueue $deepQueue): IQueueObjectManager
	{
		return new DeepQueueObjectManagerAdapter($deepQueue);
	}
	
	private function getConfigMock(IManagerPlugin $manager): IDeepQueueConfig
	{
		/**
		 * @var $configMock \PHPUnit_Framework_MockObject_MockObject|IDeepQueueConfig
		 */
		$configMock = $this->getMockBuilder(IDeepQueueConfig::class)->getMock();
		
		$configMock->expects($this->once())
			->method('manager')
			->willReturn($manager);
		
		return $configMock;
	}

	
	public function setUp()
	{
		\UnitTestScope::clear();
	}
	
	
	public function test_load()
	{
		$queueObject = new QueueObject();
		$queueObject->Name = 'test';
		
		/**
		 * @var \PHPUnit_Framework_MockObject_MockObject|IManagerPlugin $managerMock
		 */
		$managerMock = $this->getMockBuilder(IManagerPlugin::class)->getMock();
		
		$managerMock->expects($this->once())
			->method('load')
			->with($this->equalTo($queueObject->Name))
			->willReturn($queueObject);

		$dq = new DQ_Test_DeepQueueObjectManagerAdapterTest($this->getConfigMock($managerMock));
		
		$this->getSubject($dq)->load($queueObject->Name);
	}
	
	public function test_save_newObject()
	{
		$queueObject = new QueueObject();
		$queueObject->Name = 'test';
		
		/**
		 * @var \PHPUnit_Framework_MockObject_MockObject|IManagerPlugin $managerMock
		 */
		$managerMock = $this->getMockBuilder(IManagerPlugin::class)->getMock();
		
		$managerMock->expects($this->once())
			->method('create')
			->with($this->isInstanceOf(IQueueObject::class));
		
		$dq = new DQ_Test_DeepQueueObjectManagerAdapterTest($this->getConfigMock($managerMock));
		
		$this->getSubject($dq)->save($queueObject);
	}
	
	public function test_saveExistingObject()
	{
		$queueObject = new QueueObject();
		$queueObject->Id = 'test-id';
		$queueObject->Name = 'test';
		
		/**
		 * @var \PHPUnit_Framework_MockObject_MockObject|IManagerPlugin $managerMock
		 */
		$managerMock = $this->getMockBuilder(IManagerPlugin::class)->getMock();
		
		$managerMock->expects($this->once())
			->method('update')
			->with($this->isInstanceOf(IQueueObject::class));

		$dq = new DQ_Test_DeepQueueObjectManagerAdapterTest($this->getConfigMock($managerMock));
		
		$this->getSubject($dq)->save($queueObject);
	}
}


class DQ_Test_DeepQueueObjectManagerAdapterTest extends DeepQueue
{
	private $config;
	
	
	public function __construct(IDeepQueueConfig $config)
	{
		$this->config = $config;
	}
	
	public function config(): IDeepQueueConfig
	{
		return $this->config;
	}
}