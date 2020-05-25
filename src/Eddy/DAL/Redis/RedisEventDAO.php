<?php
namespace Eddy\DAL\Redis;


use Eddy\Enums\EventState;
use Eddy\Objects\EventObject;
use Eddy\DAL\Redis\Base\IRedisEventDAO;
use Eddy\DAL\Redis\Utils\RedisKeyBuilder;

use Predis\Client;

use DeepQueue\Utils\TimeBasedRandomIdGenerator;


class RedisEventDAO implements IRedisEventDAO
{
	/** @var Client */
	private $client;
	
	
	private function fromString(string $data): EventObject
	{
		$event = new EventObject();
		$event->fromArray((array)json_decode($data));
		
		return $event;
	}
	
	private function toString(EventObject $event): string
	{
		return json_encode($event->toArray());
	}

	/** 
	 * @param $events EventObject[]|array 
	 */
	private function saveAll(array $events): void
	{
		$jsonData = [];
		$nameToId = [];
		$interfaceToId = [];
		
		foreach ($events as $event)
		{
			if (!$event->Id)
			{
				$event->Id = (new TimeBasedRandomIdGenerator())->get();
			}
			
			$jsonData[$event->Id] = $this->toString($event);
			$nameToId[$event->Name] = $event->Id;
			$interfaceToId[$event->EventInterface] = $event->Id;
		}
		
		$transaction = $this->client->transaction();
		
		$transaction->hmset(RedisKeyBuilder::eventObject(), $jsonData);
		$transaction->hmset(RedisKeyBuilder::eventByName(), $nameToId);
		$transaction->hmset(RedisKeyBuilder::eventByInterface(), $interfaceToId);
		
		$transaction->execute();
	}
	
	
	public function setClient(Client $client)
	{
		$this->client = $client;
	}

	public function load(string $eventId): ?EventObject
	{
		$data = $this->client->hget(RedisKeyBuilder::eventObject(), $eventId);
		
		return $data ? $this->fromString($data) : null;
	}

	public function loadMultiple(array $ids): array
	{
		$entries = $this->client->hmget(RedisKeyBuilder::eventObject(), $ids);

		$events = [];
		
		foreach ($entries as $entry)
		{
			if ($entry)
			{
				$events[] = $this->fromString($entry);
			}
		}
				
		return $events;
	}
	
	public function loadAllRunning(): array
	{
		$allEntries = $this->client->hgetall(RedisKeyBuilder::eventObject());
		
		$running = [];

		foreach ($allEntries as $entry)
		{
			$event = $this->fromString($entry);
			
			if ($event->State == EventState::RUNNING)
			{
				$running[] = $event;
			}
		}
		
		return $running;
	}

	public function loadByIdentifier(string $identifier): ?EventObject
	{
		$event = $this->loadByInterfaceName($identifier);
		
		if (!$event)
		{
			$event = $this->loadByName($identifier);
		}
		
		return $event;
	}

	public function loadByName(string $name): ?EventObject
	{
		$id = $this->client->hget(RedisKeyBuilder::eventByName(), $name);
		
		if (!$id) return null;
		
		return $this->load($id);
	}

	public function loadByInterfaceName(string $interfaceName): ?EventObject
	{
		$id = $this->client->hget(RedisKeyBuilder::eventByInterface(), $interfaceName);
		
		if (!$id) return null;
		
		return $this->load($id);
	}

	public function saveSetup(EventObject $event): bool
	{
		$this->saveSetupAll([$event]);
		
		return true;
	}

	public function saveSetupAll(array $events): bool
	{
		$this->saveAll($events);
		
		return true;
	}

	public function updateSettings(EventObject $event): bool
	{
		if (!$event->Id) return false;
		
		$this->saveAll([$event]);
		
		return true;
	}

	public function delete(EventObject $event): bool
	{
		if (!$event->Id) return false;
		
		$transaction = $this->client->transaction();
		
		$transaction->hdel(RedisKeyBuilder::eventObject(), [$event->Id]);
		$transaction->hdel(RedisKeyBuilder::eventByName(), [$event->Name]);
		$transaction->hdel(RedisKeyBuilder::eventByInterface(), [$event->EventInterface]);
		
		$transaction->execute();
		
		return true;
	}

	public function flushAll(): void
	{
		$transaction = $this->client->transaction();
		
		$transaction->del([
			RedisKeyBuilder::eventObject(), 
			RedisKeyBuilder::eventByName(), 
			RedisKeyBuilder::eventByInterface()
		]);
		
		$transaction->execute();
	}
}