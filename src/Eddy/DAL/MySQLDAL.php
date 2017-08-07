<?php
namespace Eddy\DAL;


use Eddy\Scope;
use Eddy\Base\IDAL;
use Eddy\Base\DAL\IEventDAO;
use Eddy\Base\DAL\IHandlerDAO;
use Eddy\Base\DAL\ISubscribersDAO;
use Eddy\DAL\MySQL\Base\IMySQLEventDAO;
use Eddy\DAL\MySQL\Base\IMySQLHandlerDAO;
use Eddy\DAL\MySQL\Base\IMySQLSubscribersDAO;

use Squid\MySql\IMySqlConnector;


class MySQLDAL implements IDAL
{
	/** @var IMySqlConnector */
	private $connector;
	
	/** @var IHandlerDAO */
	private $handlerDAO;
	
	/** @var IEventDAO */
	private $eventDAO;
	
	/** @var ISubscribersDAO */
	private $subscribersDAO;
	
	
	public function __construct(IMySqlConnector $connector)
	{
		$this->connector = $connector;
	}

	
	public function handlers(): IHandlerDAO
	{
		if (!$this->handlerDAO)
		{
			$this->handlerDAO = Scope::skeleton(IMySQLHandlerDAO::class);
			$this->handlerDAO->setConnector($this->connector);
		}
		
		return $this->handlerDAO;
	}

	public function events(): IEventDAO
	{
		if (!$this->eventDAO)
		{
			$this->eventDAO = Scope::skeleton(IMySQLEventDAO::class);
			$this->eventDAO->setConnector($this->connector);
		}
		
		return $this->eventDAO;
	}

	public function subscribers(): ISubscribersDAO
	{
		if (!$this->subscribersDAO)
		{
			$this->subscribersDAO = Scope::skeleton(IMySQLSubscribersDAO::class);
			$this->subscribersDAO->setConnector($this->connector);
		}
		
		return $this->subscribersDAO;
	}
}