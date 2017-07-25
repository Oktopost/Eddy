<?php
namespace Eddy\DAL;


use Eddy\Base\IDAL;
use Eddy\Base\Module\DAO\IEventDAO;
use Eddy\Base\Module\DAO\IHandlerDAO;
use Squid\MySql\IMySqlConnector;


class MySQLDAL implements IDAL
{
	public function __construct(IMySqlConnector $config)
	{
		
	}

	public function handlers(): IHandlerDAO
	{
		// TODO: Implement handlers() method.
	}

	public function events(): IEventDAO
	{
		// TODO: Implement events() method.
	}

	public function subscribers(): IEventDAO
	{
		// TODO: Implement subscribers() method.
	}

	public function addInvoker(array $invokerToEvent)
	{
		// TODO: Implement addInvoker() method.
	}
}