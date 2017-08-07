<?php
namespace Eddy\DAL\MySQL;


use DeepQueue\Utils\TimeBasedRandomIdGenerator;
use Eddy\DAL\MySQL\Connector\EventConnector;
use Eddy\DAL\MySQL\Connector\HandlerConnector;
use Eddy\Object\EventObject;
use Eddy\Object\HandlerObject;
use lib\MySQLConfig;
use PHPUnit\Framework\TestCase;
use Squid\MySql\Command\ICmdDelete;
use Squid\MySql\Command\IDml;
use Squid\MySql\Connection\IMySqlConnection;
use Squid\MySql\Connection\IMySqlExecutor;
use Squid\MySql\Impl\Connection\ConnectionBuilder;
use Squid\MySql\Impl\Connection\MySqlConnectionDecorator;
use Squid\MySql\IMySqlConnector;


class MySQLSubscribersDAOTest extends TestCase
{
	private const SUBSCRIBERS_TABLE = 'EddySubscribers';
	private const EXECUTORS_TABLE 	= 'EddyExecutors';
	
	private const EVENT_FIELD 		= 'EddyEventId';
	private const HANDLER_FIELD		= 'EddyHandlerId';
	
	
	private function getEvent(): EventObject
	{
		$connector = new EventConnector();
		$connector->setMySQL(MySQLConfig::connector());
	
		$dao = new MySQLEventDAO($connector);
		
		$eventObject = new EventObject();
		$eventObject->Name = (new TimeBasedRandomIdGenerator())->get();
		$eventObject->EventInterface = (new TimeBasedRandomIdGenerator())->get();
		$eventObject->HandlerInterface = $eventObject->EventInterface;
		
		$dao->saveSetup($eventObject);
		
		return $eventObject;
	}
	
	private function getHandler(): HandlerObject
	{
		$connector = new HandlerConnector();
		$connector->setMySQL(MySQLConfig::connector());
	
		$dao = new MySQLHandlerDAO($connector);
		
		$handlerObject = new HandlerObject();
		$handlerObject->Name = (new TimeBasedRandomIdGenerator())->get();
		$handlerObject->HandlerClassName = (new TimeBasedRandomIdGenerator())->get();
		
		$dao->saveSetup($handlerObject);
		
		return $handlerObject;
	}
	
	private function getSubject(): MySQLSubscribersDAO
	{
		$dao = new MySQLSubscribersDAO();
		$dao->setConnector(MySQLConfig::connector());
	
		return $dao;
	}
	
	private function connectionExist(string $eventId, string $handlerId): bool
	{
		$isSubscribed = MySQLConfig::connector()->select()
			->from(self::SUBSCRIBERS_TABLE)
			->byFields([self::EVENT_FIELD => $eventId, self::HANDLER_FIELD => $handlerId])
			->queryRow();
		
		return (bool)$isSubscribed;
	}
	
	private function executorExists(string $eventId, string $handlerId): bool
	{	
		$isExecute = MySQLConfig::connector()->select()
			->from(self::EXECUTORS_TABLE)
			->byFields([self::EVENT_FIELD => $eventId, self::HANDLER_FIELD => $handlerId])
			->queryRow();

		return (bool)$isExecute;
	}
	
	/**
	 * @param bool $result
	 * @return \PHPUnit_Framework_MockObject_MockObject|IMySqlConnection
	 */
	private function mockThrowableConnection(string $commandToThrow)
	{
		$mock = $this->getMockBuilder(IMySqlConnection::class)->getMock();
		
		$mock->method('execute')
					->with($this->callback(function($value) use ($commandToThrow)
					{
						if (strpos($value, $commandToThrow) !== false)
						{
							throw new \Exception('error');
						}
						
						return true;
					}))
					->willReturn(true);
		return $mock;
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
		
		\UnitTestScope::clear();
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

	/**
	 * @expectedException \Eddy\Exceptions\InvalidUsageException
	 */
	public function test_addSubscribers_EmptyArrayPassed_ExceptionThrowed()
	{
		$this->getSubject()->addSubscribers([]);
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

	public function test_AddSubscribersWithPlainElements_SubscribersExists_NewAdded()
	{
		$event = $this->getEvent();
		$event2 = $this->getEvent();
		
		$handler = $this->getHandler();
		$handler2 = $this->getHandler();
		$handler3 = $this->getHandler();
		
		$this->getSubject()->subscribe($event->Id, $handler3->Id);
		
		$this->getSubject()->addSubscribers(
			[
				$event->Id 	=> [$handler->Id, $handler2->Id], 
				$event2->Id => $handler3->Id
			]
		);
		
		self::assertTrue($this->connectionExist($event->Id, $handler->Id));
		self::assertTrue($this->connectionExist($event->Id, $handler2->Id));
		self::assertTrue($this->connectionExist($event2->Id, $handler3->Id));
	}

	/** 
	 * @expectedException \Exception 
	 */
	public function test_addSubscribers_ExecptionThrowedInDeleteInTransaction_ExistingNotTouched()
	{
		$event = $this->getEvent();
		
		$handler = $this->getHandler();
		$handler2 = $this->getHandler();
		$handler3 = $this->getHandler();

		$this->getSubject()->subscribe($event->Id, $handler3->Id);

		$connector = clone MySQLConfig::connector();
		$connector->setConnection($this->mockThrowableConnection('DELETE'));
		
		$dao = $this->getSubject();
		$dao->setConnector($connector);
		
		$dao->addSubscribers([$event->Id => [$handler->Id, $handler2->Id]]);

		self::assertTrue($this->connectionExist($event->Id, $handler3->Id));
		self::assertFalse($this->connectionExist($event->Id, $handler->Id));
		self::assertFalse($this->connectionExist($event->Id, $handler2->Id));
	}
	
	/** 
	 * @expectedException \Exception 
	 */
	public function test_addSubscribers_ExecptionThrowedInInsertInTransaction_ExistingNotTouched()
	{
		$event = $this->getEvent();
		
		$handler = $this->getHandler();
		$handler2 = $this->getHandler();
		$handler3 = $this->getHandler();

		$this->getSubject()->subscribe($event->Id, $handler3->Id);

		$connector = clone MySQLConfig::connector();
		$connector->setConnection($this->mockThrowableConnection('INSERT IGNORE'));
		
		$dao = $this->getSubject();
		$dao->setConnector($connector);
		
		$dao->addSubscribers([$event->Id => [$handler->Id, $handler2->Id]]);

		self::assertTrue($this->connectionExist($event->Id, $handler3->Id));
		self::assertFalse($this->connectionExist($event->Id, $handler->Id));
		self::assertFalse($this->connectionExist($event->Id, $handler2->Id));
	}
	
	public function test_addSubscribersByNames()
	{
		$event = $this->getEvent();
		$event2 = $this->getEvent();
		$event3 = $this->getEvent();
		
		$handler = $this->getHandler();
		$handler2 = $this->getHandler();
		$handler3 = $this->getHandler();
		
		$config = [
			$event->Name => [
				$handler->Name,
				$handler2->Name
			],
			$event2->Name => $handler3->Name,
			$event3->Name => [
				$handler->Name,
				$handler3->Name
			]
		];
		
		$this->getSubject()->addSubscribersByNames($config);
		
		self::assertTrue($this->connectionExist($event->Id, $handler->Id));
		self::assertTrue($this->connectionExist($event->Id, $handler2->Id));
		self::assertTrue($this->connectionExist($event2->Id, $handler3->Id));
		self::assertTrue($this->connectionExist($event3->Id, $handler->Id));
		self::assertTrue($this->connectionExist($event3->Id, $handler3->Id));
	}

	/**
	 * @expectedException \Eddy\Exceptions\InvalidUsageException
	 */
	public function test_addSubscribersByNames_EmptyArray()
	{
		$this->getSubject()->addSubscribersByNames([]);
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