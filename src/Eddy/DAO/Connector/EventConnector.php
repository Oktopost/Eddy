<?php
namespace Eddy\DAO\Connector;


use Eddy\Object\EventObject;
use Eddy\Base\DAO\Connector\IEventConnector;

use Objection\Mappers;

use Squid\MySql\IMySqlConnector;
use Squid\MySql\Impl\Connectors\Object\Generic\GenericIdConnector;


class EventConnector extends GenericIdConnector implements IEventConnector
{
	private const TABLE = 'EddyEvent';
	
	
	public function __construct()
	{
		parent::__construct();
		
		$mapper = Mappers::simple();
		$mapper->setDefaultClassName(EventObject::class);
		
		$this
			->setTable(self::TABLE)
			->setIdKey('Id')
			->setObjectMap($mapper);
	}


	public function setMySQL(IMySqlConnector $mysql): IEventConnector
	{
		$this->setConnector($mysql);
		return $this;
	}
}