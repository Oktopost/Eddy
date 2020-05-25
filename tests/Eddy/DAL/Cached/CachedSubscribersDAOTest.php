<?php
namespace Eddy\DAL\Cached;


use DeepQueue\Utils\TimeBasedRandomIdGenerator;
use Eddy\Base\DAL\IEventDAO;
use Eddy\Base\DAL\IHandlerDAO;
use Eddy\Base\IDAL;
use Eddy\DAL\MySQLDAL;
use Eddy\DAL\Redis\RedisHandlerDAO;
use Eddy\DAL\RedisDAL;
use Eddy\Objects\EventObject;
use Eddy\Objects\HandlerObject;
use lib\MySQLConfig;
use PHPUnit\Framework\TestCase;
use Predis\Client;


class CachedSubscribersDAOTest extends TestCase
{
	private const SUBSCRIBERS_TABLE = 'EddySubscribers';
	private const EXECUTORS_TABLE 	= 'EddyExecutors';
	
	private const SCOPE			= 'EddyCacheTest:';
	
	
	/** @var IDAL */
	private $main;
	
	/** @var IDAL */
	private $cache;
	
	
	private function getSubject(): CachedSubscribersDAO
	{
		$cachedSubscribersDAO = new CachedSubscribersDAO();
		$cachedSubscribersDAO->setMain($this->main->subscribers());
		$cachedSubscribersDAO->setCache($this->cache->subscribers());
		
		return $cachedSubscribersDAO;
	}
	
	private function getEvent(): EventObject
	{
		$eventObject = new EventObject();
		$eventObject->Name = (new TimeBasedRandomIdGenerator())->get();
		$eventObject->EventInterface = (new TimeBasedRandomIdGenerator())->get();
		$eventObject->HandlerInterface = $eventObject->EventInterface;
		
		$this->main->events()->saveSetup($eventObject);
		
		return $eventObject;
	}
	
	private function getHandler(): HandlerObject
	{
		$handlerObject = new HandlerObject();
		$handlerObject->Name = (new TimeBasedRandomIdGenerator())->get();
		$handlerObject->HandlerClassName = (new TimeBasedRandomIdGenerator())->get();
		
		$this->main->handlers()->saveSetup($handlerObject);
		
		return $handlerObject;
	}
	
	private function getClient(): Client
	{
		return new Client([], ['prefix' => self::SCOPE]);
	}
	
	private function connectionExistInMain(string $eventId, string $handlerId): bool
	{
		$isSubscribed = MySQLConfig::connector()->select()
			->from(self::SUBSCRIBERS_TABLE)
			->byFields(['EddyEventId' => $eventId, 'EddyHandlerId' => $handlerId])
			->queryRow();
		
		return (bool)$isSubscribed;
	}
	
	private function connectionExistInCache(string $eventId, string $handlerId): bool
	{
		$isEventHadlerSet = $this->getClient()->hexists('EventHandlers:' . $eventId, $handlerId);
		$isHandlerEventSet = $this->getClient()->hexists('HandlerEvents:' . $handlerId, $eventId);

		return $isEventHadlerSet && $isHandlerEventSet;
	}
	
	private function executorExistsInMain(string $eventId, string $handlerId): bool
	{	
		$isExecute = MySQLConfig::connector()->select()
			->from(self::EXECUTORS_TABLE)
			->byFields(['EddyEventId' => $eventId, 'EddyHandlerId' => $handlerId])
			->queryRow();

		return (bool)$isExecute;
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
		
		$this->getClient()->eval("return redis.call('del', 'defaultKey', unpack(redis.call('keys', ARGV[1])))", 
			0, self::SCOPE . '*');
		
		$this->main = new MySQLDAL(MySQLConfig::connector());
		$this->cache = new RedisDAL($this->getClient());
	}


	/**
	 * @expectedException \Eddy\Exceptions\InvalidUsageException
	 */
	public function test_setCache_WrongDAO_GotException()
	{
		$this->getSubject()->setCache(new RedisHandlerDAO());
	}
	
	public function test_subscribe_CacheEmpty_EmptyAfterSubscribe()
	{
		$event = $this->getEvent();
		$handler = $this->getHandler();
		
		self::assertFalse($this->connectionExistInCache($event->Id, $handler->Id));
		
		$this->getSubject()->subscribe($event->Id, $handler->Id);
		
		self::assertTrue($this->connectionExistInMain($event->Id, $handler->Id));
		self::assertFalse($this->connectionExistInCache($event->Id, $handler->Id));
	}
	
	public function test_subscribe_CacheNotEmpty_EmptyAfterSubscribe()
	{
		$event = $this->getEvent();
		$handler = $this->getHandler();
		
		$anotherHandler = $this->getHandler();
		
		$this->cache->subscribers()->subscribe($event->Id, $handler->Id);
		
		self::assertTrue($this->connectionExistInCache($event->Id, $handler->Id));
		
		$this->getSubject()->subscribe($event->Id, $anotherHandler->Id);
		
		self::assertFalse($this->connectionExistInCache($event->Id, $handler->Id));
		self::assertFalse($this->connectionExistInCache($event->Id, $anotherHandler->Id));
		self::assertTrue($this->connectionExistInMain($event->Id, $anotherHandler->Id));
	}
	
	public function test_unsubscribe_CacheEmpty_EmptyAfterUnsubscribe()
	{
		$event = $this->getEvent();
		$handler = $this->getHandler();
		
		$this->main->subscribers()->subscribe($event->Id, $handler->Id);
		
		self::assertFalse($this->connectionExistInCache($event->Id, $handler->Id));
		self::assertTrue($this->connectionExistInMain($event->Id, $handler->Id));

		$this->getSubject()->unsubscribe($event->Id, $handler->Id);

		self::assertFalse($this->connectionExistInMain($event->Id, $handler->Id));
		self::assertFalse($this->connectionExistInCache($event->Id, $handler->Id));
	}
	
	public function test_unsubscribe_CacheNotEmpty_EmptyAfterUnsubscribe()
	{
		$event = $this->getEvent();
		$handler = $this->getHandler();
		
		$anotherHandler = $this->getHandler();
		
		$this->cache->subscribers()->subscribe($event->Id, $handler->Id);
		$this->main->subscribers()->subscribe($event->Id, $anotherHandler->Id);

		self::assertTrue($this->connectionExistInCache($event->Id, $handler->Id));
		self::assertTrue($this->connectionExistInMain($event->Id, $anotherHandler->Id));

		$this->getSubject()->unsubscribe($event->Id, $anotherHandler->Id);		

		self::assertFalse($this->connectionExistInMain($event->Id, $handler->Id));
		self::assertFalse($this->connectionExistInCache($event->Id, $handler->Id));
		self::assertFalse($this->connectionExistInCache($event->Id, $anotherHandler->Id));
	}
	
	public function test_getHandlersIds_NotInCacheNotInMain_GotEmptyArray()
	{
		self::assertEmpty($this->getSubject()->getHandlersIds(1));
	}
	
	public function test_getHandlersIds_NotInCache_PuttedInCacheAfterLoad()
	{
		$event = $this->getEvent();
		$handler = $this->getHandler();
		$handler2 = $this->getHandler();
		
		$this->main->subscribers()->addSubscribers([$event->Id => [$handler->Id, $handler2->Id]]);
		
		self::assertTrue($this->connectionExistInMain($event->Id, $handler->Id));
		self::assertTrue($this->connectionExistInMain($event->Id, $handler2->Id));
		
		self::assertFalse($this->connectionExistInCache($event->Id, $handler->Id));
		self::assertFalse($this->connectionExistInCache($event->Id, $handler2->Id));
		
		$handlerIds = $this->getSubject()->getHandlersIds($event->Id);
		
		self::assertEquals(2, sizeof($handlerIds));
		self::assertTrue(in_array($handler->Id, $handlerIds) && in_array($handler2->Id, $handlerIds));

		self::assertTrue($this->connectionExistInMain($event->Id, $handler->Id));
		self::assertTrue($this->connectionExistInMain($event->Id, $handler2->Id));
		
		self::assertTrue($this->connectionExistInCache($event->Id, $handler->Id));
		self::assertTrue($this->connectionExistInCache($event->Id, $handler2->Id));
	}
	
	public function test_getHandlersIds_InCache_ReturnedFromCache()
	{
		$event = $this->getEvent();
		$handler = $this->getHandler();
		$handler2 = $this->getHandler();
		
		$this->cache->subscribers()->addSubscribers([$event->Id => [$handler->Id, $handler2->Id]]);
		
		self::assertFalse($this->connectionExistInMain($event->Id, $handler->Id));
		self::assertFalse($this->connectionExistInMain($event->Id, $handler2->Id));
		
		self::assertTrue($this->connectionExistInCache($event->Id, $handler->Id));
		self::assertTrue($this->connectionExistInCache($event->Id, $handler2->Id));
		
		$handlerIds = $this->getSubject()->getHandlersIds($event->Id);
		
		self::assertEquals(2, sizeof($handlerIds));
		self::assertTrue(in_array($handler->Id, $handlerIds) && in_array($handler2->Id, $handlerIds));

		self::assertFalse($this->connectionExistInMain($event->Id, $handler->Id));
		self::assertFalse($this->connectionExistInMain($event->Id, $handler2->Id));
		
		self::assertTrue($this->connectionExistInCache($event->Id, $handler->Id));
		self::assertTrue($this->connectionExistInCache($event->Id, $handler2->Id));
	}
	
	public function test_getEventsIds_NotInCacheNotInMain_GotEmptyArray()
	{
		self::assertEmpty($this->getSubject()->getEventsIds(1));
	}
	
	public function test_getEventsIds_NotInCache_PuttedInCacheAfterLoad()
	{
		$event = $this->getEvent();
		$handler = $this->getHandler();
		$handler2 = $this->getHandler();
		
		$this->main->subscribers()->addSubscribers([$event->Id => [$handler->Id, $handler2->Id]]);
		
		self::assertTrue($this->connectionExistInMain($event->Id, $handler->Id));
		self::assertTrue($this->connectionExistInMain($event->Id, $handler2->Id));
		
		self::assertFalse($this->connectionExistInCache($event->Id, $handler->Id));
		self::assertFalse($this->connectionExistInCache($event->Id, $handler2->Id));
		
		$eventIds = $this->getSubject()->getEventsIds($handler->Id);
	
		self::assertEquals(1, sizeof($eventIds));
		self::assertTrue(in_array($event->Id, $eventIds));
		self::assertTrue($this->connectionExistInMain($event->Id, $handler->Id));
		self::assertTrue($this->connectionExistInCache($event->Id, $handler->Id));

		$eventIds2 = $this->getSubject()->getEventsIds($handler2->Id);
		
		self::assertEquals(1, sizeof($eventIds2));
		self::assertTrue(in_array($event->Id, $eventIds2));
		self::assertTrue($this->connectionExistInMain($event->Id, $handler2->Id));
		self::assertTrue($this->connectionExistInCache($event->Id, $handler2->Id));
	}
	
	public function test_getEventsIds_InCache_ReturnedFromCache()
	{
		$event = $this->getEvent();
		$handler = $this->getHandler();
		$handler2 = $this->getHandler();
		
		$this->cache->subscribers()->addSubscribers([$event->Id => [$handler->Id, $handler2->Id]]);
		
		self::assertFalse($this->connectionExistInMain($event->Id, $handler->Id));
		self::assertFalse($this->connectionExistInMain($event->Id, $handler2->Id));
		
		self::assertTrue($this->connectionExistInCache($event->Id, $handler->Id));
		self::assertTrue($this->connectionExistInCache($event->Id, $handler2->Id));
		
		$eventIds = $this->getSubject()->getEventsIds($handler->Id);
		
		self::assertEquals(1, sizeof($eventIds));
		self::assertTrue(in_array($event->Id, $eventIds));
		self::assertFalse($this->connectionExistInMain($event->Id, $handler->Id));
		self::assertTrue($this->connectionExistInCache($event->Id, $handler->Id));
		
		$eventIds2 = $this->getSubject()->getEventsIds($handler2->Id);
		
		self::assertEquals(1, sizeof($eventIds2));
		self::assertTrue(in_array($event->Id, $eventIds));
		
		self::assertFalse($this->connectionExistInMain($event->Id, $handler2->Id));
		self::assertTrue($this->connectionExistInCache($event->Id, $handler2->Id));
	}
	
	public function test_addSubscribers_CacheEmpty_EmptyAfterAddSubscribers()
	{
		$event = $this->getEvent();
		$handler = $this->getHandler();
		
		self::assertFalse($this->connectionExistInCache($event->Id, $handler->Id));
		
		$this->getSubject()->addSubscribers([$event->Id => $handler->Id]);
		
		self::assertTrue($this->connectionExistInMain($event->Id, $handler->Id));
		self::assertFalse($this->connectionExistInCache($event->Id, $handler->Id));
	}
	
	public function test_addSubscribers_CacheNotEmpty_EmptyAfterAddSubscribers()
	{
		$event = $this->getEvent();
		$handler = $this->getHandler();
		
		$anotherHandler = $this->getHandler();
		
		$this->cache->subscribers()->addSubscribers([$event->Id => $handler->Id]);
		
		self::assertTrue($this->connectionExistInCache($event->Id, $handler->Id));
		
		$this->getSubject()->addSubscribers([$event->Id => $anotherHandler->Id]);
		
		self::assertFalse($this->connectionExistInCache($event->Id, $handler->Id));
		self::assertFalse($this->connectionExistInCache($event->Id, $anotherHandler->Id));
		self::assertTrue($this->connectionExistInMain($event->Id, $anotherHandler->Id));
	}
	
	public function test_addSubscribersByNames_CacheEmpty_EmptyAfterAddSubscribersByNames()
	{
		$event = $this->getEvent();
		$handler = $this->getHandler();
		
		self::assertFalse($this->connectionExistInCache($event->Id, $handler->Id));
		
		$this->getSubject()->addSubscribersByNames([$event->Name => $handler->Name]);
		
		self::assertTrue($this->connectionExistInMain($event->Id, $handler->Id));
		self::assertFalse($this->connectionExistInCache($event->Id, $handler->Id));
	}
	
	public function test_addSubscribersByNames_CacheNotEmpty_EmptyAfterAddSubscribersByNames()
	{
		$event = $this->getEvent();
		$handler = $this->getHandler();
		
		$this->cache->events()->saveSetup($event);
		$this->cache->handlers()->saveSetup($handler);
		
		$anotherHandler = $this->getHandler();
		
		$this->cache->subscribers()->addSubscribersByNames([$event->Name => $handler->Name]);
		
		self::assertTrue($this->connectionExistInCache($event->Id, $handler->Id));
		
		$this->getSubject()->addSubscribersByNames([$event->Name => $anotherHandler->Name]);
		
		self::assertFalse($this->connectionExistInCache($event->Id, $handler->Id));
		self::assertFalse($this->connectionExistInCache($event->Id, $anotherHandler->Id));
		self::assertTrue($this->connectionExistInMain($event->Id, $anotherHandler->Id));
	}
	
	public function test_addExecutor_CacheEmpty_CacheStillEmpty()
	{
		$event = $this->getEvent();
		$handler = $this->getHandler();
		
		self::assertFalse($this->connectionExistInCache($event->Id, $handler->Id));
		
		$this->getSubject()->addExecutor($handler->Id, $event->Id);
		
		self::assertFalse($this->connectionExistInCache($event->Id, $handler->Id));
		self::assertTrue($this->executorExistsInMain($event->Id, $handler->Id));
	}
	
	public function test_addExecutor_CacheNotEmpty_CacheStillNotEmpty()
	{
		$event = $this->getEvent();
		$handler = $this->getHandler();
		
		$this->cache->subscribers()->subscribe($event->Id, $handler->Id);
		
		self::assertTrue($this->connectionExistInCache($event->Id, $handler->Id));
		
		$this->getSubject()->addExecutor($handler->Id, $event->Id);
		
		self::assertTrue($this->connectionExistInCache($event->Id, $handler->Id));
		self::assertTrue($this->executorExistsInMain($event->Id, $handler->Id));
	}
	
	public function test_flushAll_CacheCleared()
	{
		$event = $this->getEvent();
		$handler = $this->getHandler();
		
		$this->cache->subscribers()->subscribe($event->Id, $handler->Id);
		
		$event2 = $this->getEvent();
		$handler2 = $this->getHandler();
		
		$this->cache->subscribers()->subscribe($event2->Id, $handler2->Id);
		
		self::assertTrue($this->connectionExistInCache($event->Id, $handler->Id));
		self::assertTrue($this->connectionExistInCache($event2->Id, $handler2->Id));
		
		$this->getSubject()->flushAll();
		
		self::assertFalse($this->connectionExistInCache($event->Id, $handler->Id));
		self::assertFalse($this->connectionExistInCache($event2->Id, $handler2->Id));
	}
}