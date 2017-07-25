<?php
namespace Eddy\DAL;


use Eddy\Scope;
use Eddy\Base\IDAL;
use Eddy\Base\DAL\IEventDAO;
use Eddy\Base\DAL\IHandlerDAO;
use Eddy\Base\DAL\ISubscribersDAO;

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
		
		$this->handlerDAO = Scope::skeleton(IHandlerDAO::class);
		$this->handlerDAO->setConnector($connector);
		
		$this->eventDAO = Scope::skeleton(IEventDAO::class);
		$this->eventDAO->setConnector($connector);
		
		$this->subscribersDAO = Scope::skeleton(ISubscribersDAO::class);
		$this->subscribersDAO->setConnector($connector);
	}

	
	public function handlers(): IHandlerDAO
	{
		return $this->handlerDAO;
	}

	public function events(): IEventDAO
	{
		return $this->eventDAO;
	}

	public function subscribers(): ISubscribersDAO
	{
		return $this->subscribersDAO;
	}

	public function addInvoker(array $invokerToEvent)
	{
	}
}