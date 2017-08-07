<?php
namespace Eddy\DAL\Redis;


use Eddy\Object\HandlerObject;
use Eddy\DAL\Redis\Base\IRedisHandlerDAO;

use Predis\Client;

use DeepQueue\Utils\TimeBasedRandomIdGenerator;


class RedisHandlerDAO implements IRedisHandlerDAO
{
	private const HANDLER_OBJECTS 	= 'HandlerObjects';
	private const BY_NAME			= 'HandlerObjectsByName';
	private const BY_CLASSNAME		= 'HandlerObjectsByClassName';
	
	
	/** @var Client */
	private $client;
	
	
	private function fromString(string $data): HandlerObject
	{
		$handler = new HandlerObject();
		$handler->fromArray((array)json_decode($data));
		
		return $handler;
	}
	
	private function toString(HandlerObject $handler): string
	{
		return json_encode($handler->toArray());
	}

	/** 
	 * @param $handlers HandlerObject[]|array 
	 */
	private function saveAll(array $handlers): void
	{
		$jsonData = [];
		$nameToId = [];
		$classNameToId = [];
		
		foreach ($handlers as $handler)
		{
			if (!$handler->Id)
			{
				$handler->Id = (new TimeBasedRandomIdGenerator())->get();
			}
			
			$jsonData[$handler->Id] = $this->toString($handler);
			$nameToId[$handler->Name] = $handler->Id;
			$classNameToId[$handler->HandlerClassName] = $handler->Id;
		}
		
		$transaction = $this->client->transaction();
		
		$transaction->hmset(self::HANDLER_OBJECTS, $jsonData);
		$transaction->hmset(self::BY_NAME, $nameToId);
		$transaction->hmset(self::BY_CLASSNAME, $classNameToId);
		
		$transaction->execute();
	}
	
	
	public function setClient(Client $client)
	{
		$this->client = $client;
	}

	public function load(string $handlerId): ?HandlerObject
	{
		$data = $this->client->hget(self::HANDLER_OBJECTS, $handlerId);
		
		return $data ? $this->fromString($data) : null;
	}

	public function loadMultiple(array $ids): array
	{
		$entries = $this->client->hmget(self::HANDLER_OBJECTS, $ids);

		$handlers = [];
		
		foreach ($entries as $entry)
		{
			if ($entry)
			{
				$handlers[] = $this->fromString($entry);
			}
		}
				
		return $handlers;
	}

	public function loadByIdentifier(string $identifier): ?HandlerObject
	{
		$handler = $this->loadByClassName($identifier);
		
		if (!$handler)
		{
			$handler = $this->loadByName($identifier);
		}
		
		return $handler;
	}

	public function loadByName(string $name): ?HandlerObject
	{
		$id = $this->client->hget(self::BY_NAME, $name);
		
		if (!$id) return null;
		
		return $this->load($id);
	}

	public function loadByClassName(string $className): ?HandlerObject
	{
		$id = $this->client->hget(self::BY_CLASSNAME, $className);
		
		if (!$id) return null;
		
		return $this->load($id);
	}

	public function saveSetup(HandlerObject $handler): bool
	{
		$this->saveSetupAll([$handler]);
		
		return true;
	}

	public function saveSetupAll(array $handlers): bool
	{
		$this->saveAll($handlers);
		
		return true;
	}

	public function updateSettings(HandlerObject $handler): bool
	{
		if (!$handler->Id) return false;
		
		$this->saveAll([$handler]);
		
		return true;
	}

	public function delete(HandlerObject $handler): bool
	{
		if (!$handler->Id) return false;
		
		$transaction = $this->client->transaction();
		
		$transaction->hdel(self::HANDLER_OBJECTS, [$handler->Id]);
		$transaction->hdel(self::BY_NAME, [$handler->Name]);
		$transaction->hdel(self::BY_CLASSNAME, [$handler->HandlerClassName]);
		
		$transaction->execute();
		
		return true;
	}
}