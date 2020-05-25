<?php
namespace Eddy\DAL\MySQL\Connector;


use Eddy\Objects\EventObject;
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
		
		$this
			->setTable(self::TABLE)
			->setIdKey('Id')
			->setObjectMap(EventObject::class, ['Created', 'Modified']);
	}


	public function setMySQL(IMySqlConnector $mysql): IEventConnector
	{
		$this->setConnector($mysql);
		return $this;
	}
}