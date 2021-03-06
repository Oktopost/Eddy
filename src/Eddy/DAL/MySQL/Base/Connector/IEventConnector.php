<?php
namespace Eddy\DAL\MySQL\Base\Connector;


use Squid\MySql\IMySqlConnector;
use Squid\MySql\Connectors\Objects\Generic\IGenericIdConnector;


/**
 * @skeleton
 */
interface IEventConnector extends IGenericIdConnector
{
	public function setMySQL(IMySqlConnector $mysql): IEventConnector;	
}