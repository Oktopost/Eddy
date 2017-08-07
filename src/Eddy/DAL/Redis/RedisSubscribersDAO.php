<?php
namespace Eddy\DAL\Redis;


use Eddy\DAL\Redis\Base\IRedisSubscribersDAO;

use Predis\Client;


class RedisSubscribersDAO implements IRedisSubscribersDAO
{
	/** @var Client */
	private $client;
	
	
	public function setClient(Client $client)
	{
		$this->client = $client;
	}

	public function subscribe(string $eventId, string $handlerId): void
	{
		// TODO: Implement subscribe() method.
	}

	public function unsubscribe(string $eventId, string $handlerId): void
	{
		// TODO: Implement unsubscribe() method.
	}

	public function getHandlersIds(string $eventId): array
	{
		// TODO: Implement getHandlersIds() method.
	}

	public function getEventsIds(string $handlerId): array
	{
		// TODO: Implement getEventsIds() method.
	}

	public function addSubscribers(array $eventToHandlers): void
	{
		// TODO: Implement addSubscribers() method.
	}

	public function addSubscribersByNames(array $eventNamesToHandlers): void
	{
		// TODO: Implement addSubscribersByNames() method.
	}

	public function addExecutor(string $handlerId, string $eventId): void
	{
		// TODO: Implement addExecutor() method.
	}
}