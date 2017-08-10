<?php
namespace Eddy\DAL\Redis;


use Eddy\Enums\EventState;
use Eddy\Object\HandlerObject;
use Eddy\DAL\Redis\Base\IRedisHandlerDAO;
use Eddy\DAL\Redis\Utils\RedisKeyBuilder;

use Predis\Client;

use DeepQueue\Utils\TimeBasedRandomIdGenerator;


class RedisHandlerDAO implements IRedisHandlerDAO
{
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
		
		$transaction->hmset(RedisKeyBuilder::handlerObject(), $jsonData);
		$transaction->hmset(RedisKeyBuilder::handlerByName(), $nameToId);
		$transaction->hmset(RedisKeyBuilder::handlerByClassName(), $classNameToId);
		
		$transaction->execute();
	}
	
	
	public function setClient(Client $client)
	{
		$this->client = $client;
	}

	public function load(string $handlerId): ?HandlerObject
	{
		$data = $this->client->hget(RedisKeyBuilder::handlerObject(), $handlerId);
		
		return $data ? $this->fromString($data) : null;
	}

	public function loadMultiple(array $ids): array
	{
		$entries = $this->client->hmget(RedisKeyBuilder::handlerObject(), $ids);

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
	
	public function loadAllRunning(): array
	{
		$allEntries = $this->client->hgetall(RedisKeyBuilder::handlerObject());
		
		$running = [];

		foreach ($allEntries as $entry)
		{
			$handler = $this->fromString($entry);
			
			if ($handler->State == EventState::RUNNING)
			{
				$running[] = $handler;
			}
		}
		
		return $running;
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
		$id = $this->client->hget(RedisKeyBuilder::handlerByName(), $name);
		
		if (!$id) return null;
		
		return $this->load($id);
	}

	public function loadByClassName(string $className): ?HandlerObject
	{
		$id = $this->client->hget(RedisKeyBuilder::handlerByClassName(), $className);
		
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
		
		$transaction->hdel(RedisKeyBuilder::handlerObject(), [$handler->Id]);
		$transaction->hdel(RedisKeyBuilder::handlerByName(), [$handler->Name]);
		$transaction->hdel(RedisKeyBuilder::handlerByClassName(), [$handler->HandlerClassName]);
		
		$transaction->execute();
		
		return true;
	}
	
	public function flushAll(): void
	{
		$transaction = $this->client->transaction();
		
		$transaction->del([
			RedisKeyBuilder::handlerObject(), 
			RedisKeyBuilder::handlerByName(), 
			RedisKeyBuilder::handlerByClassName()
		]);
		
		$transaction->execute();
	}
}