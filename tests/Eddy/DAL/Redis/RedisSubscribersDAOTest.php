<?php
namespace Eddy\DAL\Redis;


use DeepQueue\Utils\TimeBasedRandomIdGenerator;
use Eddy\Object\EventObject;
use Eddy\Object\HandlerObject;
use PHPUnit\Framework\TestCase;

use Predis\Client;


class RedisSubscribersDAOTest extends TestCase
{
	private const EVENT_HANDLERS_PREFIX	= 'EventHandlers:';
	private const HANDLER_EVENTS_PREFIX	= 'HandlerEvents:';
	
	
	private function getClient(): Client
	{
		return new Client([], ['prefix' => 'redis.subscribers-test:']);
	}
	
	private function getEvent(): EventObject
	{
		$dao = new RedisEventDAO();
		$dao->setClient($this->getClient());
		
		$eventObject = new EventObject();
		$eventObject->Name = (new TimeBasedRandomIdGenerator())->get();
		$eventObject->EventInterface = (new TimeBasedRandomIdGenerator())->get();
		$eventObject->HandlerInterface = $eventObject->EventInterface;
		
		$dao->saveSetup($eventObject);
		
		return $eventObject;
	}
	
	private function getHandler(): HandlerObject
	{
		$dao = new RedisHandlerDAO();
		$dao->setClient($this->getClient());
		
		$handlerObject = new HandlerObject();
		$handlerObject->Name = (new TimeBasedRandomIdGenerator())->get();
		$handlerObject->HandlerClassName = (new TimeBasedRandomIdGenerator())->get();
		
		$dao->saveSetup($handlerObject);
		
		return $handlerObject;
	}
	
	private function getSubject(): RedisSubscribersDAO
	{
		$dao = new RedisSubscribersDAO();
		$dao->setClient($this->getClient());
	
		return $dao;
	}
	
	private function connectionExist(string $eventId, string $handlerId): bool
	{
		$isEventHadlerSet = $this->getClient()->hexists(self::EVENT_HANDLERS_PREFIX . $eventId, $handlerId);
		$isHandlerEventSet = $this->getClient()->hexists(self::HANDLER_EVENTS_PREFIX . $handlerId, $eventId);

		return $isEventHadlerSet && $isHandlerEventSet;
	}
	
	private function executorExists(string $eventId, string $handlerId): bool
	{	
		return false;
	}
	
	
	public function setUp()
	{
		$this->getClient()->eval("return redis.call('del', 'defaultKey', unpack(redis.call('keys', ARGV[1])))", 
			0, 'redis.subscribers-test:*');
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