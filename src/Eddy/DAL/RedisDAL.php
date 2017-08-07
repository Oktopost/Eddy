<?php
namespace Eddy\DAL;


use Eddy\Scope;
use Eddy\Base\IDAL;
use Eddy\Base\DAL\IEventDAO;
use Eddy\Base\DAL\IHandlerDAO;
use Eddy\Base\DAL\ISubscribersDAO;
use Eddy\DAL\Redis\Base\IRedisEventDAO;
use Eddy\DAL\Redis\Base\IRedisHandlerDAO;
use Eddy\DAL\Redis\Base\IRedisSubscribersDAO;

use Predis\Client;


class RedisDAL implements IDAL
{
	/** @var Client */
	private $client;
	
	/** @var IHandlerDAO */
	private $handlerDAO;
	
	/** @var IEventDAO */
	private $eventDAO;
	
	/** @var ISubscribersDAO */
	private $subscribersDAO;
	
	
	public function __construct(Client $client)
	{
		$this->client = $client;
	}


	public function handlers(): IHandlerDAO
	{
		if (!$this->handlerDAO)
		{
			$this->handlerDAO = Scope::skeleton(IRedisHandlerDAO::class);
			$this->handlerDAO->setClient($this->client);
		}
		
		return $this->handlerDAO;
	}

	public function events(): IEventDAO
	{
		if (!$this->eventDAO)
		{
			$this->eventDAO = Scope::skeleton(IRedisEventDAO::class);
			$this->eventDAO->setClient($this->client);
		}
		
		return $this->eventDAO;
	}

	public function subscribers(): ISubscribersDAO
	{
		if (!$this->subscribersDAO)
		{
			$this->subscribersDAO = Scope::skeleton(IRedisSubscribersDAO::class);
			$this->subscribersDAO->setClient($this->client);
		}
		
		return $this->subscribersDAO;
	}
}