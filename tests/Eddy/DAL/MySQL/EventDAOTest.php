<?php
namespace Eddy\DAL\MySQL;


use DeepQueue\Utils\TimeBasedRandomIdGenerator;
use Eddy\Base\DAL\IEventDAO;
use Eddy\Object\EventObject;
use Eddy\DAL\MySQL\Connector\EventConnector;

use lib\MySQLConfig;

use PHPUnit\Framework\TestCase;


class EventDAOTest extends TestCase
{
	private const EVENT_TABLE = 'EddyEvent';
	
	
	private function getSubject(): IEventDAO
	{
		$connector = new EventConnector();
		$connector->setMySQL(MySQLConfig::connector());
	
		return new EventDAO($connector);
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
		$name = MySQLConfig::connector()->select()
			->from(self::EVENT_TABLE)
			->column($column)
			->byField('Id', $id)
			->queryColumn(true);
		
		return $name[0];
	}
	
	public function setUp()
	{
		MySQLConfig::connector()->delete()
			->where('1 = 1')
			->from(self::EVENT_TABLE)
			->executeDml();
	}

	public function test_saveSetup_newEvent()
	{
		$eventObject = $this->getEvent();
		
		$this->getSubject()->saveSetup($eventObject);
		
		self::assertNotNull($eventObject->Id);
		self::assertEquals($eventObject->Name, $this->getColumnById('Name', $eventObject->Id));
	}

	public function test_saveSetup_updateExistingEvent()
	{
		$eventObject = $this->getEvent(true);

		$eventObject->Name = 'TestNewEvent';
		$eventObject->EventInterface = 'NewTestInterface';
		$this->getSubject()->saveSetup($eventObject);
		
		self::assertEquals($eventObject->EventInterface, 
			$this->getColumnById('EventInterface', $eventObject->Id));
		self::assertNotEquals($eventObject->Name, $this->getColumnById('Name', $eventObject->Id));
	}
	
	public function test_updateSettings_newEvent_GotFalse()
	{
		self::assertFalse($this->getSubject()->updateSettings($this->getEvent()));
	}
	
	public function test_updateSettings_existingEvent_EventSettingsUpdated()
	{
		$event = $this->getEvent(true);
		$event->Delay = 10;
		$event->EventInterface = 'newTest';
		
		$this->getSubject()->updateSettings($event);
		
		self::assertEquals($event->Delay, $this->getColumnById('Delay', $event->Id));
		self::assertNotEquals($event->EventInterface, $this->getColumnById('EventInterface', $event->Id));
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
}