<?php
namespace Eddy\Modules;


use Eddy\Base\DAL\IEventDAO;
use Eddy\Base\DAL\IHandlerDAO;
use Eddy\Base\IConfig;
use Eddy\Base\IDAL;
use Eddy\Base\Module\IEddyObjectModule;
use Eddy\Enums\EventState;
use Eddy\Objects\EventObject;
use Eddy\Objects\HandlerObject;
use Eddy\Scope;
use Eddy\Utils\Config;
use Eddy\Utils\Naming;
use PHPUnit\Framework\TestCase;


class EddyObjectModuleTest extends TestCase
{
	private function getSubject(?IHandlerDAO $handlerDAO = null, ?IEventDAO $eventDAO = null): IEddyObjectModule
	{
		$config = new TestEddyObjectModule_Config($this->getDALMock($handlerDAO, $eventDAO));
		
		$obj = new \stdClass();
		Scope::skeleton()->context($obj, 'test')->set(IConfig::class, $config);
		
		$eddyObjectModule = Scope::skeleton($obj, IEddyObjectModule::class);
		
		return $eddyObjectModule;
	}
	
	/**
	 * @return \PHPUnit_Framework_MockObject_MockObject|IDAL
	 */
	private function getDALMock(?IHandlerDAO $handlerDAO = null, ?IEventDAO $eventDAO = null): IDAL
	{
		$dal = $this->getMockBuilder(IDAL::class)->getMock();

		if ($handlerDAO)
		{
			$dal->method('handlers')
				->willReturn($handlerDAO);
		}
		
		if ($eventDAO)
		{
			$dal->method('events')
				->willReturn($eventDAO);
		}

		return $dal;
	}
	
	
	protected function setUp()
	{
		\UnitTestScope::clear();
	}
	
	
	public function test_getByQueueName_UnknownType_ReturnNull()
	{
		self::assertNull($this->getSubject()->getByQueueName('unknown_type_name'));
	}
	
	public function test_getByQueueName_EventQueueName_ReturnEventObject()
	{
		$eventObject = new EventObject();
		$eventObject->Name = 'TestEvent';
		
		$eventDAO = $this->getMockBuilder(IEventDAO::class)->getMock();
		$eventDAO->expects($this->once())
			->method('loadByName')
			->with($this->equalTo($eventObject->Name))
			->willReturn($eventObject);
		
		self::assertInstanceOf(EventObject::class, 
			$this->getSubject(null, $eventDAO)
				->getByQueueName($eventObject->getQueueNaming(new Naming())));
	}
	
	public function test_getByQueueName_HandlerQueueName_ReturnHandlerObject()
	{
		$handlerObject = new HandlerObject();
		$handlerObject->Name = 'TestHandler';
		
		$handlerDAO = $this->getMockBuilder(IHandlerDAO::class)->getMock();
		$handlerDAO->expects($this->once())
			->method('loadByName')
			->with($this->equalTo($handlerObject->Name))
			->willReturn($handlerObject);
		
		self::assertInstanceOf(HandlerObject::class, 
			$this->getSubject($handlerDAO)
				->getByQueueName($handlerObject->getQueueNaming(new Naming())));
	}
	
	public function test_getAllRunning_NoRunning_GotEmptyArray()
	{
		$handlerDAO = $this->getMockBuilder(IHandlerDAO::class)->getMock();
		$handlerDAO->expects($this->once())
			->method('loadAllRunning')
			->willReturn([]);
		
		$eventDAO = $this->getMockBuilder(IEventDAO::class)->getMock();
		$eventDAO->expects($this->once())
			->method('loadAllRunning')
			->willReturn([]);
		
		self::assertEmpty($this->getSubject($handlerDAO, $eventDAO)->getAllRunning());
	}
	
	public function test_getAllRunning_RunningExists_GotArray()
	{
		$handlerObject = new HandlerObject();
		$handlerObject->State = EventState::RUNNING;
		$handlerObject->Name = 'TestHandler';
		
		
		$handlerDAO = $this->getMockBuilder(IHandlerDAO::class)->getMock();
		$handlerDAO->expects($this->once())
			->method('loadAllRunning')
			->willReturn([$handlerObject]);
		
		$eventObject = new EventObject();
		$eventObject->State = EventState::RUNNING;
		$eventObject->Name = 'TestEvent';
		
		$eventDAO = $this->getMockBuilder(IEventDAO::class)->getMock();
		$eventDAO->expects($this->once())
			->method('loadAllRunning')
			->willReturn([$eventObject]);
		
		self::assertEquals(2, count($this->getSubject($handlerDAO, $eventDAO)->getAllRunning()));
	}
}


class TestEddyObjectModule_Config extends Config
{
	private $dal;
	
	public function __construct(IDAL $dal)
	{
		$this->dal = $dal;
		
		parent::__construct();
	}
	
	public function DAL(): IDAL
	{
		return $this->dal;
	}
}