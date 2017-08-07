<?php
namespace Eddy\DAL\Redis;


use Eddy\Object\HandlerObject;
use Eddy\DAL\Redis\Base\IRedisHandlerDAO;

use Predis\Client;


class RedisHandlerDAO implements IRedisHandlerDAO
{
	/** @var Client */
	private $client;
	
	
	public function setClient(Client $client)
	{
		$this->client = $client;
	}

	public function load(string $id): ?HandlerObject
	{
		// TODO: Implement load() method.
	}

	public function loadMultiple(array $ids): array
	{
		// TODO: Implement loadMultiple() method.
	}

	public function loadByIdentifier(string $identifier): ?HandlerObject
	{
		// TODO: Implement loadByIdentifier() method.
	}

	public function loadByName(string $name): ?HandlerObject
	{
		// TODO: Implement loadByName() method.
	}

	public function loadByClassName(string $className): ?HandlerObject
	{
		// TODO: Implement loadByClassName() method.
	}

	public function saveSetup(HandlerObject $handler): bool
	{
		// TODO: Implement saveSetup() method.
	}

	public function saveSetupAll(array $handlers): bool
	{
		// TODO: Implement saveSetupAll() method.
	}

	public function updateSettings(HandlerObject $handler): bool
	{
		// TODO: Implement updateSettings() method.
	}

	public function delete(HandlerObject $handler): bool
	{
		// TODO: Implement delete() method.
	}
}