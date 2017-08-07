<?php
namespace Eddy\DAL\Redis;


use Eddy\Object\EventObject;
use Eddy\DAL\Redis\Base\IRedisEventDAO;

use Predis\Client;


class RedisEventDAO implements IRedisEventDAO
{
	/** @var Client */
	private $client;
	
	
	public function setClient(Client $client)
	{
		$this->client = $client;
	}

	public function load(string $eventId): ?EventObject
	{
		// TODO: Implement load() method.
	}

	public function loadMultiple(array $ids): array
	{
		// TODO: Implement loadMultiple() method.
	}

	public function loadByIdentifier(string $identifier): ?EventObject
	{
		// TODO: Implement loadByIdentifier() method.
	}

	public function loadByName(string $name): ?EventObject
	{
		// TODO: Implement loadByName() method.
	}

	public function loadByInterfaceName(string $interfaceName): ?EventObject
	{
		// TODO: Implement loadByInterfaceName() method.
	}

	public function saveSetup(EventObject $event): bool
	{
		// TODO: Implement saveSetup() method.
	}

	public function saveSetupAll(array $events): bool
	{
		// TODO: Implement saveSetupAll() method.
	}

	public function updateSettings(EventObject $event): bool
	{
		// TODO: Implement updateSettings() method.
	}

	public function delete(EventObject $event): bool
	{
		// TODO: Implement delete() method.
	}
}