<?php
namespace Eddy\DAL\Cached;


use Eddy\Base\IDAL;
use Eddy\DAL\MySQLDAL;
use Eddy\DAL\RedisDAL;
use Eddy\DAL\Redis\RedisEventDAO;
use Eddy\Enums\EventState;
use Eddy\Object\HandlerObject;

use lib\MySQLConfig;

use DeepQueue\Utils\TimeBasedRandomIdGenerator;

use PHPUnit\Framework\TestCase;

use Predis\Client;


class CachedHandlerDAOTest extends TestCase
{
	private const HANDLER_TABLE = 'EddyHandler';
	private const SCOPE			= 'EddyCacheTest:';
	
	/** @var IDAL */
	private $main;
	
	/** @var IDAL */
	private $cache;
	
	
	private function getSubject(): CachedHandlerDAO
	{
		$cachedHandlerDAO = new CachedHandlerDAO();
		$cachedHandlerDAO->setMain($this->main->handlers());
		$cachedHandlerDAO->setCache($this->cache->handlers());
		
		return $cachedHandlerDAO;
	}
	
	private function getHandler(bool $saved = false): HandlerObject
	{
		$handlerObject = new HandlerObject();
		$handlerObject->Name = (new TimeBasedRandomIdGenerator())->get();
		$handlerObject->HandlerClassName = (new TimeBasedRandomIdGenerator())->get();
		$handlerObject->Delay = 5;
		
		if ($saved)
		{
			$this->main->handlers()->saveSetup($handlerObject);
		}
		
		return $handlerObject;
	}
	
	private function getClient(): Client
	{
		return new Client([], ['prefix' => self::SCOPE]);
	}
	
	
	public function setUp()
	{
		MySQLConfig::connector()->delete()
			->where('1 = 1')
			->from(self::HANDLER_TABLE)
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
		$this->getSubject()->setCache(new RedisEventDAO());
	}
	
	public function test_load_firstTimeFromMainAndPutInCache_GotHandlerObject()
	{
		$handler = $this->getHandler(true);
		
		self::assertNull($this->cache->handlers()->load($handler->Id));
		self::assertInstanceOf(HandlerObject::class, $this->getSubject()->load($handler->Id));
		self::assertNotNull($this->cache->handlers()->load($handler->Id));
	}
	
	public function test_load_secondCallLoadFromCache_GotHandlerObject()
	{
		$handler = $this->getHandler(true);
		$this->getSubject()->load($handler->Id);
		
		$this->main->handlers()->delete($handler);
		
		self::assertNull($this->main->handlers()->load($handler->Id));
		self::assertNotNull($this->getSubject()->load($handler->Id));
	}
	
	public function test_loadMultiple_NothingFoundInCacheAndInMain_GotEmptyArray()
	{
		self::assertEmpty($this->getSubject()->loadMultiple([1,2,3]));
	}
	
	public function test_loadMultiple_NothingFoundInCacheAllFoundInMain_GotArrayOfHandlers()
	{
		$handler = $this->getHandler(true);
		$handler2 = $this->getHandler(true);
		
		self::assertEmpty($this->cache->handlers()->loadMultiple([$handler->Id, $handler2->Id]));
		self::assertEquals(2, count($this->getSubject()->loadMultiple([$handler->Id, $handler2->Id])));
		self::assertEquals(2, count($this->cache->handlers()->loadMultiple([$handler->Id, $handler2->Id])));
	}
	
	public function test_loadMultiple_PartFoundInCacheAllFoundInMain_GotArrayOfHandlers()
	{
		$handler = $this->getHandler(true);
		
		$handler2 = $this->getHandler(true);
		
		$handler->Name = 'cached';
		
		$this->cache->handlers()->saveSetup($handler);
		
		$handlers = $this->getSubject()->loadMultiple([$handler->Id, $handler2->Id]);
		
		self::assertEquals(2, count($handlers));
		self::assertEquals($handler->Name, $handlers[0]->Name);
	}
	
	public function test_loadMultiple_AllFoundInCache_GotArrayOfHandlers()
	{
		$handler = $this->getHandler(true);
		$handler2 = $this->getHandler(true);
		
		$handler->Name = 'cached';
		$handler2->Name = 'cached2';
		
		$this->cache->handlers()->saveSetupAll([$handler, $handler2]);
		
		$handlers = $this->getSubject()->loadMultiple([$handler->Id, $handler2->Id]);
		
		self::assertEquals(2, count($handlers));
		self::assertEquals($handler->Name, $handlers[0]->Name);
		self::assertEquals($handler2->Name, $handlers[1]->Name);
	}
	
	public function test_loadByIdentifier_firstTimeFromMainAndPutInCache_GotHandlerObject()
	{
		$handler = $this->getHandler(true);
		
		self::assertNull($this->cache->handlers()->loadByIdentifier($handler->Name));
		self::assertInstanceOf(HandlerObject::class, $this->getSubject()->loadByIdentifier($handler->Name));
		self::assertNotNull($this->cache->handlers()->loadByIdentifier($handler->Name));
	}
	
	public function test_loadByIdentifier_secondCallLoadFromCache_GotHandlerObject()
	{
		$handler = $this->getHandler(true);
		$this->getSubject()->loadByIdentifier($handler->Name);
		
		$this->main->handlers()->delete($handler);
		
		self::assertNull($this->main->handlers()->loadByIdentifier($handler->Name));
		self::assertNotNull($this->getSubject()->loadByIdentifier($handler->Name));
	}
	
	public function test_loadByName_firstTimeFromMainAndPutInCache_GotHandlerObject()
	{
		$handler = $this->getHandler(true);
		
		self::assertNull($this->cache->handlers()->loadByName($handler->Name));
		self::assertInstanceOf(HandlerObject::class, $this->getSubject()->loadByName($handler->Name));
		self::assertNotNull($this->cache->handlers()->loadByName($handler->Name));
	}
	
	public function test_loadByName_secondCallLoadFromCache_GotHandlerObject()
	{
		$handler = $this->getHandler(true);
		$this->getSubject()->loadByName($handler->Name);
		
		$this->main->handlers()->delete($handler);
		
		self::assertNull($this->main->handlers()->loadByName($handler->Name));
		self::assertNotNull($this->getSubject()->loadByName($handler->Name));
	}
	
	public function test_loadByClassName_firstTimeFromMainAndPutInCache_GotHandlerObject()
	{
		$handler = $this->getHandler(true);
		
		self::assertNull($this->cache->handlers()->loadByClassName($handler->HandlerClassName));
		self::assertInstanceOf(HandlerObject::class, 
			$this->getSubject()->loadByClassName($handler->HandlerClassName));
		self::assertNotNull($this->cache->handlers()->loadByClassName($handler->HandlerClassName));
	}
	
	public function test_loadByClassNameName_secondCallLoadFromCache_GotHandlerObject()
	{
		$handler = $this->getHandler(true);
		$this->getSubject()->loadByClassName($handler->HandlerClassName);
		
		$this->main->handlers()->delete($handler);
		
		self::assertNull($this->main->handlers()->loadByClassName($handler->HandlerClassName));
		self::assertNotNull($this->getSubject()->loadByClassName($handler->HandlerClassName));
	}
	
	public function test_saveSetup_NotInCache_Saved_StillNotInCache()
	{
		$handler = $this->getHandler(true);
		
		self::assertNull($this->cache->handlers()->load($handler->Id));
		
		$this->getSubject()->saveSetup($handler);
		
		self::assertNull($this->cache->handlers()->load($handler->Id));
	}
	
	public function test_saveSetup_inCache_Saved_RemovedFromCache()
	{
		$handler = $this->getHandler(true);
		
		$this->cache->handlers()->saveSetup($handler);
		
		self::assertNotNull($this->cache->handlers()->load($handler->Id));
		
		$this->getSubject()->saveSetup($handler);
		
		self::assertNull($this->cache->handlers()->load($handler->Id));
	}
	
	public function test_saveSetupAll_CacheCleared()
	{
		$handler = $this->getHandler(true);
		$handler2 = $this->getHandler(true);
		
		$this->cache->handlers()->saveSetupAll([$handler, $handler2]);
		
		self::assertEquals(2, count($this->cache->handlers()->loadMultiple([$handler->Id, $handler2->Id])));
		
		$this->getSubject()->saveSetupAll([$handler, $handler2]);
		
		self::assertEmpty($this->cache->handlers()->loadMultiple([$handler->Id, $handler2->Id]));
	}
	
	public function test_updateSettings_NotInCache_Saved_StillNotInCache()
	{
		$handler = $this->getHandler(true);
		
		self::assertNull($this->cache->handlers()->load($handler->Id));
		
		$this->getSubject()->updateSettings($handler);
		
		self::assertNull($this->cache->handlers()->load($handler->Id));
	}
	
	public function test_updateSettings_inCache_Saved_RemovedFromCache()
	{
		$handler = $this->getHandler(true);
		
		$this->cache->handlers()->saveSetup($handler);
		
		self::assertNotNull($this->cache->handlers()->load($handler->Id));
		
		$this->getSubject()->updateSettings($handler);
		
		self::assertNull($this->cache->handlers()->load($handler->Id));
	}
	
	public function test_delete_removedFromCache()
	{
		$handler = $this->getHandler(true);
		
		$this->cache->handlers()->saveSetup($handler);
		
		self::assertNotNull($this->cache->handlers()->load($handler->Id));
		
		$this->getSubject()->delete($handler);
		
		self::assertNull($this->cache->handlers()->load($handler->Id));
	}
	
	public function test_flushAll_CacheCleared()
	{
		$handler = $this->getHandler(true);
		$handler2 = $this->getHandler(true);
		
		$this->cache->handlers()->saveSetupAll([$handler, $handler2]);
		
		self::assertEquals(2, count($this->cache->handlers()->loadMultiple([$handler->Id, $handler2->Id])));
		
		$this->getSubject()->flushAll();
		
		self::assertEmpty($this->cache->handlers()->loadMultiple([$handler->Id, $handler2->Id]));
	}
	
	public function test_loadAllRunning_LoadFromMain()
	{
		$handler = $this->getHandler();
		$handler->State = EventState::RUNNING;
		
		$this->getSubject()->saveSetup($handler);
		
		$running = $this->getSubject()->loadAllRunning();
		
		self::assertEquals(1, sizeof($running));
		self::assertEquals($handler->Name, $running[0]->Name);
		
		self::assertEmpty($this->cache->handlers()->loadAllRunning());
	}
}