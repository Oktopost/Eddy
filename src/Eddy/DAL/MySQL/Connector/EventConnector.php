<?php
namespace Eddy\DAL\MySQL\Connector;


use Eddy\Object\EventObject;
use Eddy\DAL\MySQL\Base\Connector\IEventConnector;

use Objection\Mappers;

use Squid\MySql\IMySqlConnector;
use Squid\MySql\Impl\Connectors\Object\Generic\GenericIdConnector;


/**
 * @autoload
 */
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