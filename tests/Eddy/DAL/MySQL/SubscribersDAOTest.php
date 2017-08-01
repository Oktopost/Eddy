<?php
namespace Eddy\DAL\MySQL;


use DeepQueue\Utils\TimeBasedRandomIdGenerator;
use Eddy\DAL\MySQL\Connector\EventConnector;
use Eddy\DAL\MySQL\Connector\HandlerConnector;
use Eddy\Object\EventObject;
use Eddy\Object\HandlerObject;
use lib\MySQLConfig;
use PHPUnit\Framework\TestCase;


class SubscribersDAOTest extends TestCase
{
	private const SUBSCRIBERS_TABLE = 'EddySubscribers';
	private const EXECUTORS_TABLE 	= 'EddyExecutors';
	
	private const EVENT_FIELD 		= 'EddyEventId';
	private const HANDLER_FIELD		= 'EddyHandlerId';
	
	
	private function getEvent(): EventObject
	{
		$connector = new EventConnector();
		$connector->setMySQL(MySQLConfig::connector());
	
		$dao = new EventDAO($connector);
		
		$eventObject = new EventObject();
		$eventObject->Name = (new TimeBasedRandomIdGenerator())->get();
		$eventObject->EventInterface = (new TimeBasedRandomIdGenerator())->get();
		$eventObject->HandlerInterface = $eventObject->EventInterface;
		
		$dao->save($eventObject);
		
		return $eventObject;
	}
	
	private function getHandler(): HandlerObject
	{
		$connector = new HandlerConnector();
		$connector->setMySQL(MySQLConfig::connector());
	
		$dao = new HandlerDAO($connector);
		
		$handlerObject = new HandlerObject();
		$handlerObject->Name = (new TimeBasedRandomIdGenerator())->get();
		$handlerObject->HandlerClassName = (new TimeBasedRandomIdGenerator())->get();
		
		$dao->save($handlerObject);
		
		return $handlerObject;
	}
	
	private function getSubject(): SubscribersDAO
	{
		$dao = new SubscribersDAO();
		$dao->setConnector(MySQLConfig::connector());
	
		return $dao;
	}
	
	private function connectionExist(string $eventId, string $handlerId): bool
	{
		$isSubscribed = MySQLConfig::connector()->select()
			->from(self::SUBSCRIBERS_TABLE)
			->byFields([self::EVENT_FIELD => $eventId, self::HANDLER_FIELD => $handlerId])
			->queryExists();
		return $isSubscribed;
	}
	
	private function executorExists(string $eventId, string $handlerId): bool
	{	
		$isExecute = MySQLConfig::connector()->select()
			->from(self::EXECUTORS_TABLE)
			->byFields([self::EVENT_FIELD => $eventId, self::HANDLER_FIELD => $handlerId])
			->queryExists();
		return $isExecute;
	}
	
	
	public function setUp()
	{
		foreach (MySQLConfig::TABLES as $table)
		{
			MySQLConfig::connector()->delete()
			->where('1 = 1')
			->from($table)
			->executeDml();
		}
	}
	
	
	public function test_subscribe()
	{
		$event = $this->getEvent();
		$handler = $this->getHandler();
		
		$this->getSubject()->subscribe($event->Id, $handler->Id);
		
		self::assertTrue($this->connectionExist($event->Id, $handler->Id));
	}
	
	public function test_unsubscribe()
	{
		$event = $this->getEvent();
		$handler = $this->getHandler();
		
		$this->getSubject()->subscribe($event->Id, $handler->Id);
		$this->getSubject()->unsubscribe($event->Id, $handler->Id);
		
		self::assertFalse($this->connectionExist($event->Id, $handler->Id));
	}
	
	public function test_getHandlersIds_NoHandlers_GotEmptyArray()
	{
		self::assertEmpty($this->getSubject()->getHandlersIds($this->getEvent()->Id));
	}
	
	public function test_getHandlersIds_HandlersExist_GotIdsArray()
	{
		$event = $this->getEvent();
		$handler = $this->getHandler();
		
		$this->getSubject()->subscribe($event->Id, $handler->Id);
		
		$handler2 = $this->getHandler();
		
		$this->getSubject()->subscribe($event->Id, $handler2->Id);
		
		$handlerIds = $this->getSubject()->getHandlersIds($event->Id);
		
		self::assertEquals(2, sizeof($handlerIds));
		self::assertEquals($handler2->Id, $handlerIds[1]);
	}
	
	public function test_getEventsIds_NoEvents_GotEmptyArray()
	{
		self::assertEmpty($this->getSubject()->getEventsIds($this->getHandler()->Id));
	}
	
	public function test_getEventsIds_EventsExist_GotIdsArray()
	{
		$handler = $this->getHandler();
		$event = $this->getEvent();
		
		$this->getSubject()->subscribe($event->Id, $handler->Id);
		
		$event2 = $this->getEvent();
		
		$this->getSubject()->subscribe($event2->Id, $handler->Id);
		
		$eventsIds = $this->getSubject()->getEventsIds($handler->Id);
		
		self::assertEquals(2, sizeof($eventsIds));
		self::assertEquals($event2->Id, $eventsIds[1]);
	}
	
	public function test_addSubscribers_NoSubscribers_NewAdded()
	{
		$event = $this->getEvent();
		
		$handler = $this->getHandler();
		$handler2 = $this->getHandler();
		
		$this->getSubject()->addSubscribers([$event->Id => [$handler->Id, $handler2->Id]]);
		
		self::assertTrue($this->connectionExist($event->Id, $handler->Id));
		self::assertTrue($this->connectionExist($event->Id, $handler2->Id));
	}
	
	public function test_addSubscribers_SubscribersExists_NewAdded()
	{
		$event = $this->getEvent();
		
		$handler = $this->getHandler();
		$handler2 = $this->getHandler();
		$handler3 = $this->getHandler();
		
		$this->getSubject()->subscribe($event->Id, $handler3->Id);
		
		$this->getSubject()->addSubscribers([$event->Id => [$handler->Id, $handler2->Id, $handler3->Id]]);
		
		self::assertTrue($this->connectionExist($event->Id, $handler->Id));
		self::assertTrue($this->connectionExist($event->Id, $handler2->Id));
		self::assertTrue($this->connectionExist($event->Id, $handler3->Id));
	}
	
	public function test_addSubscribers_SubscribersExists_OldRemovedNewAdded()
	{
		$event = $this->getEvent();
		
		$handler = $this->getHandler();
		$handler2 = $this->getHandler();
		$handler3 = $this->getHandler();
		
		$this->getSubject()->subscribe($event->Id, $handler3->Id);
		
		$this->getSubject()->addSubscribers([$event->Id => [$handler->Id, $handler2->Id]]);
		
		self::assertTrue($this->connectionExist($event->Id, $handler->Id));
		self::assertTrue($this->connectionExist($event->Id, $handler2->Id));
		self::assertFalse($this->connectionExist($event->Id, $handler3->Id));
	}
	
	public function test_addExecutor()
	{
		$handler = $this->getHandler();
		
		$event = $this->getEvent();
		
		$this->getSubject()->addExecutor($handler->Id, $event->Id);
		
		self::assertTrue($this->executorExists($event->Id, $handler->Id));
	}
	
	public function test_addExecutors_SameExecutorTwoTimes_NoErrors()
	{
		$handler = $this->getHandler();
		
		$event = $this->getEvent();
		
		$this->getSubject()->addExecutor($handler->Id, $event->Id);
		$this->getSubject()->addExecutor($handler->Id, $event->Id);
		
		self::assertTrue($this->executorExists($event->Id, $handler->Id));
	}
}