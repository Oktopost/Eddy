<?php
namespace Eddy\Base\Module\DAO\Connector;


use Squid\MySql\IMySqlConnector;
use Squid\MySql\Connectors\Object\Generic\IGenericIdConnector;


/**
 * @skeleton
 */
interface IHandlerConnector extends IGenericIdConnector
{
	public function setMySQL(IMySqlConnector $mysql): IHandlerConnector;	
}