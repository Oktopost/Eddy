<?php
namespace Eddy\DAL\MySQL\Base;


use Squid\MySql\IMySqlConnector;


interface IMySQLConnectorConsumer
{
	/**
	 * @return static
	 */
	public function setConnector(IMySqlConnector $connector);
}