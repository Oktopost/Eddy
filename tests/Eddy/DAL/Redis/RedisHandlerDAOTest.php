<?php
namespace Eddy\DAL\Redis;


use Eddy\DAL\Redis\Base\IRedisHandlerDAO;
use Eddy\Enums\EventState;

use DeepQueue\Utils\TimeBasedRandomIdGenerator;

use Eddy\Object\HandlerObject;
use PHPUnit\Framework\TestCase;
use Predis\Client;


class RedisHandlerDAOTest extends TestCase
{
	private const HANDLER_OBJECTS_KEY = 'HandlerObjects';
	
	
	private function getClient(): Client
	{
		return new Client([], ['prefix' => 'eddyhandler-test:']);
	}
	
	private function getSubject(): IRedisHandlerDAO
	{
		$dao = new RedisHandlerDAO();
		$dao->setClient($this->getClient());
		
		return $dao;
	}
	
	private function getHandler(bool $saved = false): HandlerObject
	{
		$handlerObject = new HandlerObject();
		$handlerObject->Name = (new TimeBasedRandomIdGenerator())->get();
		$handlerObject->HandlerClassName = (new TimeBasedRandomIdGenerator())->get();
		$handlerObject->Delay = 5;
		
		if ($saved)
		{
			$this->getSubject()->saveSetup($handlerObject);
		}
		
		return $handlerObject;
	}
	
	private function getColumnById(string $column, string $id): string 
	{
		$data = $this->getClient()->hget(self::HANDLER_OBJECTS_KEY, $id);
		
		$dataArray = (array)json_decode($data);
		
		return $dataArray[$column];
	}
	
	
	public function setUp()
	{
		$this->getClient()->eval("return redis.call('del', 'defaultKey', unpack(redis.call('keys', ARGV[1])))", 
			0, 'eddyhandler-test:*');
	}

	public function test_saveSetup_newHandler()
	{
		$handlerObject = $this->getHandler();
		
		$this->getSubject()->saveSetup($handlerObject);
		
		self::assertNotNull($handlerObject->Id);
		self::assertEquals($handlerObject->Name, $this->getColumnById('Name', $handlerObject->Id));
	}

	public function test_saveSetup_updateExistingHandler_AllFieldsUpdated()
	{
		$handlerObject = $this->getHandler(true);

		$handlerObject->HandlerClassName = 'TestNewHandler';
		$handlerObject->Name = 'NewName';
		$this->getSubject()->saveSetup($handlerObject);
		
		self::assertEquals($handlerObject->HandlerClassName, 
			$this->getColumnById('HandlerClassName', $handlerObject->Id));
		
		self::assertEquals($handlerObject->Name, $this->getColumnById('Name', $handlerObject->Id));
	}
	
	public function test_updateSettings_newHandler_GotFalse()
	{
		self::assertFalse($this->getSubject()->updateSettings($this->getHandler()));
	}
	
	public function test_updateSettings_newHandler_HandlerUpdated()
	{
		$handler = $this->getHandler(true);
		$handler->Delay = 10;
		$handler->HandlerClassName = 'TestNewHandler';
		$handler->State = EventState::PAUSED;
		
		$this->getSubject()->updateSettings($handler);
		
		self::assertEquals($handler->Delay, $this->getColumnById('Delay', $handler->Id));
		self::assertEquals($handler->HandlerClassName, 
			$this->getColumnById('HandlerClassName', $handler->Id));
		
		self::assertEquals($handler->State, $this->getColumnById('State', $handler->Id));
	}

	public function test_load_noHandlerExist_NullReturned()
	{
		self::assertNull($this->getSubject()->load('1'));
	}

	public function test_load_HandlerExist_HandlerObjectReturned()
	{
		$handlerObject = $this->getHandler(true);
		
		$loadedHandler = $this->getSubject()->load($handlerObject->Id);
		
		self::assertInstanceOf(HandlerObject::class, $loadedHandler);
		self::assertEquals($handlerObject->Id, $loadedHandler->Id);
	}

	public function test_loadMultiple_NoHandler_GotEmptyArray()
	{
		self::assertEmpty($this->getSubject()->loadMultiple([1,2,3]));
	}

	public function test_loadMultiple_HandlersExists_GotHandlerObjectArray()
	{
		$handlerObject = $this->getHandler(true);
		$handlerObject2 = $this->getHandler(true);
		
		$handlers = $this->getSubject()->loadMultiple([$handlerObject->Id, $handlerObject2->Id]);
		
		self::assertEquals(2, sizeof($handlers));
		self::assertInstanceOf(HandlerObject::class, $handlers[0]);
		self::assertEquals($handlerObject2->Id, $handlers[1]->Id);
	}

	public function test_loadByName_NoHandler_GotNull()
	{
		self::assertNull($this->getSubject()->loadByName('not-existing'));
	}

	public function test_loadByName_HandlerExist_GotHandlerObject()
	{
		$handlerObject = $this->getHandler(true);
		
		$loadedObject = $this->getSubject()->loadByName($handlerObject->Name);
		
		self::assertEquals($handlerObject->Id, $loadedObject->Id);
	}

	public function test_loadByClassName_NoHandler_GotNull()
	{
		self::assertNull($this->getSubject()->loadByClassName('not-existing-classname'));
	}

	public function test_loadByClassName_HandlerExist_GotHandlerObject()
	{
		$handlerObject = $this->getHandler(true);
		
		$loadedObject = $this->getSubject()->loadByClassName($handlerObject->HandlerClassName);
		
		self::assertEquals($handlerObject->Id, $loadedObject->Id);
	}

	public function test_loadByIdentifier_NoHandler_GotNull()
	{
		self::assertNull($this->getSubject()->loadByIdentifier('not-existing'));
	}

	public function test_loadByIdentifier_ExistByName_GotHandlerObject()
	{
		$handlerObject = $this->getHandler(true);
		
		$loadedObject = $this->getSubject()->loadByIdentifier($handlerObject->Name);
		
		self::assertEquals($handlerObject->Id, $loadedObject->Id);
	}

	public function test_loadByIdentifier_ExistByClassName_GotHandlerObject()
	{
		$handlerObject = $this->getHandler(true);
		
		$loadedObject = $this->getSubject()->loadByIdentifier($handlerObject->HandlerClassName);
		
		self::assertEquals($handlerObject->Id, $loadedObject->Id);
	}
	
	public function test_delete()
	{
		$handlerObject = $this->getHandler(true);
		
		$this->getSubject()->delete($handlerObject);
		
		self::assertNull($this->getSubject()->load($handlerObject->Id));
	}
	
	public function test_flushAll()
	{
		$handlerObject = $this->getHandler(true);
		$handlerObject2 = $this->getHandler(true);
		
		$this->getSubject()->flushAll();
		
		self::assertNull($this->getSubject()->load($handlerObject->Id));
		self::assertNull($this->getSubject()->load($handlerObject2->Id));
	}
}