<?php
namespace Eddy\DAL\MySQL;


use DeepQueue\Utils\TimeBasedRandomIdGenerator;

use Eddy\Base\DAL\IHandlerDAO;
use Eddy\Object\HandlerObject;
use Eddy\DAL\MySQL\Connector\HandlerConnector;

use lib\MySQLConfig;

use PHPUnit\Framework\TestCase;


class HandlerDAOTest extends TestCase
{
	private const HANDLER_TABLE = 'EddyHandler';
	
	
	private function getSubject(): IHandlerDAO
	{
		$connector = new HandlerConnector();
		$connector->setMySQL(MySQLConfig::connector());
	
		return new HandlerDAO($connector);
	}
	
	private function getHandler(bool $saved = false): HandlerObject
	{
		$handlerObject = new HandlerObject();
		$handlerObject->Name = (new TimeBasedRandomIdGenerator())->get();
		$handlerObject->HandlerClassName = (new TimeBasedRandomIdGenerator())->get();
		
		if ($saved)
		{
			$this->getSubject()->save($handlerObject);
		}
		
		return $handlerObject;
	}
	
	
	private function getNameById(string $id): string 
	{
		$name = MySQLConfig::connector()->select()
			->from(self::HANDLER_TABLE)
			->column('Name')
			->byField('Id', $id)
			->queryColumn(true);
		
		return $name[0];
	}
	
	public function setUp()
	{
		MySQLConfig::connector()->delete()
			->where('1 = 1')
			->from(self::HANDLER_TABLE)
			->executeDml();
	}

	public function test_save_newHandler()
	{
		$handlerObject = $this->getHandler();
		
		$this->getSubject()->save($handlerObject);
		
		self::assertNotNull($handlerObject->Id);
		self::assertEquals($handlerObject->Name, $this->getNameById($handlerObject->Id));
	}

	public function test_save_updateExistingHandler()
	{
		$handlerObject = $this->getHandler(true);

		$handlerObject->Name = 'TestNewHandler';
		$this->getSubject()->save($handlerObject);
		
		self::assertEquals($handlerObject->Name, $this->getNameById($handlerObject->Id));
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
}