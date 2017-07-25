<?php
namespace Eddy\DAL\MySQL\Connector;


use Eddy\Object\EventObject;
use Eddy\DAL\MySQL\Base\Connector\IHandlerConnector;

use Objection\Mappers;

use Squid\MySql\IMySqlConnector;
use Squid\MySql\Impl\Connectors\Object\Generic\GenericIdConnector;


/**
 * @autoload
 */
class HandlerConnector extends GenericIdConnector implements IHandlerConnector
{
	private const TABLE = 'EddyHandler';
	
	
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


	public function setMySQL(IMySqlConnector $mysql): IHandlerConnector
	{
		$this->setConnector($mysql);
		return $this;
	}
}