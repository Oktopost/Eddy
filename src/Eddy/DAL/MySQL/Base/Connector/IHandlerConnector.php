<?php
namespace Eddy\DAL\MySQL\Base\Connector;


use Squid\MySql\IMySqlConnector;
use Squid\MySql\Connectors\Object\Generic\IGenericIdConnector;


/**
 * @skeleton
 */
interface IHandlerConnector extends IGenericIdConnector
{
	public function setMySQL(IMySqlConnector $mysql): IHandlerConnector;	
}