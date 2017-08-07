<?php
namespace Eddy\DAL\Redis;


use Eddy\DAL\Redis\Base\IRedisEventDAO;
use Eddy\Object\EventObject;

use DeepQueue\Utils\TimeBasedRandomIdGenerator;

use PHPUnit\Framework\TestCase;

use Predis\Client;


class RedisEventDAOTest extends TestCase
{
	private const EVENT_OBJECTS_KEY = 'EventObjects';
	
	
	private function getClient(): Client
	{
		return new Client([], ['prefix' => 'eddyevent-test:']);
	}
	
	private function getSubject(): IRedisEventDAO
	{
		$dao = new RedisEventDAO();
		$dao->setClient($this->getClient());
		
		return $dao;
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
			$this->getSubject()->saveSetup($eventObject);
		}
		
		return $eventObject;
	}
	
	private function getColumnById(string $column, string $id): string 
	{
		$data = $this->getClient()->hget(self::EVENT_OBJECTS_KEY, $id);
		
		$dataArray = (array)json_decode($data);
		
		return $dataArray[$column];
	}
	
	
	public function setUp()
	{
		$this->getClient()->eval("return redis.call('del', 'defaultKey', unpack(redis.call('keys', ARGV[1])))", 
			0, 'eddyevent-test:*');
	}

	public function test_saveSetup_newEvent()
	{
		$eventObject = $this->getEvent();
		
		$this->getSubject()->saveSetup($eventObject);
		
		self::assertNotNull($eventObject->Id);
		self::assertEquals($eventObject->Name, $this->getColumnById('Name', $eventObject->Id));
	}

	public function test_saveSetup_updateExistingEvent_AllFieldsUpdated()
	{
		$eventObject = $this->getEvent(true);

		$eventObject->Name = 'TestNewEvent';
		$eventObject->EventInterface = 'NewTestInterface';
		$this->getSubject()->saveSetup($eventObject);
		
		self::assertEquals($eventObject->EventInterface, 
			$this->getColumnById('EventInterface', $eventObject->Id));
		
		self::assertEquals($eventObject->Name, $this->getColumnById('Name', $eventObject->Id));
	}
	
	public function test_updateSettings_newEvent_GotFalse()
	{
		self::assertFalse($this->getSubject()->updateSettings($this->getEvent()));
	}
	
	public function test_updateSettings_existingEvent_EventUpdated()
	{
		$event = $this->getEvent(true);
		$event->Delay = 10;
		$event->EventInterface = 'newTest';
		
		$this->getSubject()->updateSettings($event);
		
		self::assertEquals($event->Delay, $this->getColumnById('Delay', $event->Id));
		self::assertEquals($event->EventInterface, $this->getColumnById('EventInterface', $event->Id));
	}

	public function test_load_noEventExist_NullRetured()
	{
		self::assertNull($this->getSubject()->load('1'));
	}

	public function test_load_EventExist_EventObjectReturned()
	{
		$eventObject = $this->getEvent(true);
		
		$loadedEvent = $this->getSubject()->load($eventObject->Id);
		
		self::assertInstanceOf(EventObject::class, $loadedEvent);
		self::assertEquals($eventObject->Id, $loadedEvent->Id);
	}

	public function test_loadMultiple_NoEvent_GotEmptyArray()
	{
		self::assertEmpty($this->getSubject()->loadMultiple([1,2,3]));
	}

	public function test_loadMultiple_EventsExists_GotEventObjectArray()
	{
		$eventObject = $this->getEvent(true);
		$eventObject2 = $this->getEvent(true);
		
		$events = $this->getSubject()->loadMultiple([$eventObject->Id, $eventObject2->Id]);
		
		self::assertEquals(2, sizeof($events));
		self::assertInstanceOf(EventObject::class, $events[0]);
		self::assertEquals($eventObject2->Id, $events[1]->Id);
	}

	public function test_loadByName_NoEvent_GotNull()
	{
		self::assertNull($this->getSubject()->loadByName('not-existing'));
	}

	public function test_loadByName_EventExist_GotEventObject()
	{
		$eventObject = $this->getEvent(true);
		
		$loadedObject = $this->getSubject()->loadByName($eventObject->Name);
		
		self::assertEquals($eventObject->Id, $loadedObject->Id);
	}

	public function test_loadByInterfaceName_NoEvent_GotNull()
	{
		self::assertNull($this->getSubject()->loadByInterfaceName('not-existing-interface'));
	}

	public function test_loadByInterfaceName_EventExist_GotEventObject()
	{
		$eventObject = $this->getEvent(true);
		
		$loadedObject = $this->getSubject()->loadByInterfaceName($eventObject->EventInterface);
		
		self::assertEquals($eventObject->Id, $loadedObject->Id);
	}

	public function test_loadByIdentifier_NoEvent_GotNull()
	{
		self::assertNull($this->getSubject()->loadByIdentifier('not-existing'));
	}

	public function test_loadByIdentifier_ExistByName_GotEventObject()
	{
		$eventObject = $this->getEvent(true);
		
		$loadedObject = $this->getSubject()->loadByIdentifier($eventObject->Name);
		
		self::assertEquals($eventObject->Id, $loadedObject->Id);
	}

	public function test_loadByIdentifier_ExistByInterface_GotEventObject()
	{
		$eventObject = $this->getEvent(true);
		
		$loadedObject = $this->getSubject()->loadByIdentifier($eventObject->EventInterface);
		
		self::assertEquals($eventObject->Id, $loadedObject->Id);
	}
	
	public function test_delete()
	{
		$eventObject = $this->getEvent(true);
		
		$this->getSubject()->delete($eventObject);
		
		self::assertNull($this->getSubject()->load($eventObject->Id));
	}
	
	public function test_flushAll()
	{
		$eventObject = $this->getEvent(true);
		$eventObject2 = $this->getEvent(true);
		
		$this->getSubject()->flushAll();
		
		self::assertNull($this->getSubject()->load($eventObject->Id));
		self::assertNull($this->getSubject()->load($eventObject2->Id));
	}
}