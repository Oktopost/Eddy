<?php
namespace Eddy\DAL\Cached;


use DeepQueue\Utils\TimeBasedRandomIdGenerator;
use Eddy\Base\IDAL;
use Eddy\DAL\MySQLDAL;
use Eddy\DAL\Redis\RedisHandlerDAO;
use Eddy\DAL\RedisDAL;

use Eddy\Enums\EventState;
use Eddy\Object\EventObject;
use lib\MySQLConfig;

use PHPUnit\Framework\TestCase;

use Predis\Client;


class CachedEventDAOTest extends TestCase
{
	private const EVENT_TABLE 	= 'EddyEvent';
	private const SCOPE			= 'EddyCacheTest:';
	
	/** @var IDAL */
	private $main;
	
	/** @var IDAL */
	private $cache;
	
	
	private function getSubject(): CachedEventDAO
	{
		$cachedEventDAO = new CachedEventDAO();
		$cachedEventDAO->setMain($this->main->events());
		$cachedEventDAO->setCache($this->cache->events());
		
		return $cachedEventDAO;
	}
	
	private function getEvent(bool $saved = false): EventObject
	{
		$eventObject = new EventObject();
		$eventObject->Name = (new TimeBasedRandomIdGenerator())->get();
		$eventObject->EventInterface = (new TimeBasedRandomIdGenerator())->get();
		$eventObject->HandlerInterface = $eventObject->EventInterface;
		$eventObject->Delay = 5;
		
		if ($saved)
		{
			$this->main->events()->saveSetup($eventObject);
		}
		
		return $eventObject;
	}
	
	private function getClient(): Client
	{
		return new Client([], ['prefix' => self::SCOPE]);
	}
	
	
	public function setUp()
	{
		MySQLConfig::connector()->delete()
			->where('1 = 1')
			->from(self::EVENT_TABLE)
			->executeDml();
		
		$this->getClient()->eval("return redis.call('del', 'defaultKey', unpack(redis.call('keys', ARGV[1])))", 
			0, self::SCOPE . '*');
		
		$this->main = new MySQLDAL(MySQLConfig::connector());
		$this->cache = new RedisDAL($this->getClient());
	}
	
	
	/**
	 * @expectedException \Eddy\Exceptions\InvalidUsageException 
	 */
	public function test_setCache_WrongDAO_gotException()
	{
		$this->getSubject()->setCache(new RedisHandlerDAO());
	}
	
	public function test_load_firstTimeFromMainAndPutInCache_GotEventObject()
	{
		$event = $this->getEvent(true);
		
		self::assertNull($this->cache->events()->load($event->Id));
		self::assertInstanceOf(EventObject::class, $this->getSubject()->load($event->Id));
		self::assertNotNull($this->cache->events()->load($event->Id));
	}
	
	public function test_load_secondCallLoadFromCache_GotEventObject()
	{
		$event = $this->getEvent(true);
		$this->getSubject()->load($event->Id);
		
		$this->main->events()->delete($event);
		
		self::assertNull($this->main->events()->load($event->Id));
		self::assertNotNull($this->getSubject()->load($event->Id));
	}
	
	public function test_loadMultiple_NothingFoundInCacheAndInMain_GotEmptyArray()
	{
		self::assertEmpty($this->getSubject()->loadMultiple([1,2,3]));
	}
	
	public function test_loadMultiple_NothingFoundInCacheAllFoundInMain_GotArrayOfEvents()
	{
		$event = $this->getEvent(true);
		$event2 = $this->getEvent(true);
		
		self::assertEmpty($this->cache->events()->loadMultiple([$event->Id, $event2->Id]));
		self::assertEquals(2, count($this->getSubject()->loadMultiple([$event->Id, $event2->Id])));
		self::assertEquals(2, count($this->cache->events()->loadMultiple([$event->Id, $event2->Id])));
	}
	
	public function test_loadMultiple_PartFoundInCacheAllFoundInMain_GotArrayOfEvents()
	{
		$event = $this->getEvent(true);
		
		$event2 = $this->getEvent(true);
		
		$event->Name = 'cached';
		
		$this->cache->events()->saveSetup($event);
		
		$events = $this->getSubject()->loadMultiple([$event->Id, $event2->Id]);
		
		self::assertEquals(2, count($events));
		self::assertEquals($event->Name, $events[0]->Name);
	}
	
	public function test_loadMultiple_AllFoundInCache_GotArrayOfEvents()
	{
		$event = $this->getEvent(true);
		$event2 = $this->getEvent(true);
		
		$event->Name = 'cached';
		$event2->Name = 'cached2';
		
		$this->cache->events()->saveSetupAll([$event, $event2]);
		
		$events = $this->getSubject()->loadMultiple([$event->Id, $event2->Id]);
		
		self::assertEquals(2, count($events));
		self::assertEquals($event->Name, $events[0]->Name);
		self::assertEquals($event2->Name, $events[1]->Name);
	}
	
	public function test_loadByIdentifier_firstTimeFromMainAndPutInCache_GotEventObject()
	{
		$event = $this->getEvent(true);
		
		self::assertNull($this->cache->events()->loadByIdentifier($event->Name));
		self::assertInstanceOf(EventObject::class, $this->getSubject()->loadByIdentifier($event->Name));
		self::assertNotNull($this->cache->events()->loadByIdentifier($event->Name));
	}
	
	public function test_loadByIdentifier_secondCallLoadFromCache_GotEventObject()
	{
		$event = $this->getEvent(true);
		$this->getSubject()->loadByIdentifier($event->Name);
		
		$this->main->events()->delete($event);
		
		self::assertNull($this->main->events()->loadByIdentifier($event->Name));
		self::assertNotNull($this->getSubject()->loadByIdentifier($event->Name));
	}
	
	public function test_loadByName_firstTimeFromMainAndPutInCache_GotEventObject()
	{
		$event = $this->getEvent(true);
		
		self::assertNull($this->cache->events()->loadByName($event->Name));
		self::assertInstanceOf(EventObject::class, $this->getSubject()->loadByName($event->Name));
		self::assertNotNull($this->cache->events()->loadByName($event->Name));
	}
	
	public function test_loadByName_secondCallLoadFromCache_GotEventObject()
	{
		$event = $this->getEvent(true);
		$this->getSubject()->loadByName($event->Name);
		
		$this->main->events()->delete($event);
		
		self::assertNull($this->main->events()->loadByName($event->Name));
		self::assertNotNull($this->getSubject()->loadByName($event->Name));
	}
	
	public function test_loadByInterfaceName_firstTimeFromMainAndPutInCache_GotEventObject()
	{
		$event = $this->getEvent(true);
		
		self::assertNull($this->cache->events()->loadByInterfaceName($event->EventInterface));
		self::assertInstanceOf(EventObject::class, 
			$this->getSubject()->loadByInterfaceName($event->EventInterface));
		self::assertNotNull($this->cache->events()->loadByInterfaceName($event->EventInterface));
	}
	
	public function test_loadByInterfaceName_secondCallLoadFromCache_GotEventObject()
	{
		$event = $this->getEvent(true);
		$this->getSubject()->loadByInterfaceName($event->EventInterface);
		
		$this->main->events()->delete($event);
		
		self::assertNull($this->main->events()->loadByInterfaceName($event->EventInterface));
		self::assertNotNull($this->getSubject()->loadByInterfaceName($event->EventInterface));
	}
	
	public function test_saveSetup_NotInCache_Saved_StillNotInCache()
	{
		$event = $this->getEvent(true);
		
		self::assertNull($this->cache->events()->load($event->Id));
		
		$this->getSubject()->saveSetup($event);
		
		self::assertNull($this->cache->events()->load($event->Id));
	}
	
	public function test_saveSetup_inCache_Saved_RemovedFromCache()
	{
		$event = $this->getEvent(true);
		
		$this->cache->events()->saveSetup($event);
		
		self::assertNotNull($this->cache->events()->load($event->Id));
		
		$this->getSubject()->saveSetup($event);
		
		self::assertNull($this->cache->events()->load($event->Id));
	}
	
	public function test_saveSetupAll_CacheCleared()
	{
		$event = $this->getEvent(true);
		$event2 = $this->getEvent(true);
		
		$this->cache->events()->saveSetupAll([$event, $event2]);
		
		self::assertEquals(2, count($this->cache->events()->loadMultiple([$event->Id, $event2->Id])));
		
		$this->getSubject()->saveSetupAll([$event, $event2]);
		
		self::assertEmpty($this->cache->events()->loadMultiple([$event->Id, $event2->Id]));
	}
	
	public function test_updateSettings_NotInCache_Saved_StillNotInCache()
	{
		$event = $this->getEvent(true);
		
		self::assertNull($this->cache->events()->load($event->Id));
		
		$this->getSubject()->updateSettings($event);
		
		self::assertNull($this->cache->events()->load($event->Id));
	}
	
	public function test_updateSettings_inCache_Saved_RemovedFromCache()
	{
		$event = $this->getEvent(true);
		
		$this->cache->events()->saveSetup($event);
		
		self::assertNotNull($this->cache->events()->load($event->Id));
		
		$this->getSubject()->updateSettings($event);
		
		self::assertNull($this->cache->events()->load($event->Id));
	}
	
	public function test_delete_removedFromCache()
	{
		$event = $this->getEvent(true);
		
		$this->cache->events()->saveSetup($event);
		
		self::assertNotNull($this->cache->events()->load($event->Id));
		
		$this->getSubject()->delete($event);
		
		self::assertNull($this->cache->events()->load($event->Id));
	}
	
	public function test_flushAll_CacheCleared()
	{
		$event = $this->getEvent(true);
		$event2 = $this->getEvent(true);
		
		$this->cache->events()->saveSetupAll([$event, $event2]);
		
		self::assertEquals(2, count($this->cache->events()->loadMultiple([$event->Id, $event2->Id])));
		
		$this->getSubject()->flushAll();
		
		self::assertEmpty($this->cache->events()->loadMultiple([$event->Id, $event2->Id]));
	}
	
	public function test_loadAllRunning_LoadFromMain()
	{
		$event = $this->getEvent();
		$event->State = EventState::RUNNING;
		
		$this->getSubject()->saveSetup($event);
		
		$running = $this->getSubject()->loadAllRunning();
		
		self::assertEquals(1, sizeof($running));
		self::assertEquals($event->Name, $running[0]->Name);
		
		self::assertEmpty($this->cache->events()->loadAllRunning());
	}
}