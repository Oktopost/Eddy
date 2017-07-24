<?php
namespace Eddy\Base\DAO\Connector;


use Squid\MySql\IMySqlConnector;
use Squid\MySql\Connectors\Object\Generic\IGenericIdConnector;


interface IHandlerConnector extends IGenericIdConnector
{
	public function setMySQL(IMySqlConnector $mysql): IHandlerConnector;	
}