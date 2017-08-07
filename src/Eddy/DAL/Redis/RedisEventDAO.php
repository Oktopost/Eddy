<?php
namespace Eddy\DAL\Redis;


use Eddy\Object\EventObject;
use Eddy\DAL\Redis\Base\IRedisEventDAO;

use Predis\Client;

use DeepQueue\Utils\TimeBasedRandomIdGenerator;


class RedisEventDAO implements IRedisEventDAO
{
	private const EVENT_OBJECTS	= 'EventObjects';
	private const BY_NAME		= 'EventObjectsByName';
	private const BY_INTERFACE	= 'EventObjectsByInterface';
	
	
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
		
		$transaction->hmset(self::EVENT_OBJECTS, $jsonData);
		$transaction->hmset(self::BY_NAME, $nameToId);
		$transaction->hmset(self::BY_INTERFACE, $interfaceToId);
		
		$transaction->execute();
	}
	
	
	public function setClient(Client $client)
	{
		$this->client = $client;
	}

	public function load(string $eventId): ?EventObject
	{
		$data = $this->client->hget(self::EVENT_OBJECTS, $eventId);
		
		return $data ? $this->fromString($data) : null;
	}

	public function loadMultiple(array $ids): array
	{
		$entries = $this->client->hmget(self::EVENT_OBJECTS, $ids);

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
		$id = $this->client->hget(self::BY_NAME, $name);
		
		if (!$id) return null;
		
		return $this->load($id);
	}

	public function loadByInterfaceName(string $interfaceName): ?EventObject
	{
		$id = $this->client->hget(self::BY_INTERFACE, $interfaceName);
		
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
		
		$transaction->hdel(self::EVENT_OBJECTS, [$event->Id]);
		$transaction->hdel(self::BY_NAME, [$event->Name]);
		$transaction->hdel(self::BY_INTERFACE, [$event->EventInterface]);
		
		$transaction->execute();
		
		return true;
	}
}