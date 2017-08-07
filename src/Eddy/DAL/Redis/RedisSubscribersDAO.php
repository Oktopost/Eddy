<?php
namespace Eddy\DAL\Redis;


use Eddy\DAL\Redis\Base\IRedisSubscribersDAO;
use Eddy\DAL\Redis\Utils\RedisKeyBuilder;
use Eddy\Exceptions\InvalidUsageException;

use Predis\Client;
use Predis\Transaction\MultiExec;


class RedisSubscribersDAO implements IRedisSubscribersDAO
{
	/** @var Client */
	private $client;
	
	
	private function prepareCleanUp(MultiExec $transaction): void
	{
		$prefix = $this->client->getOptions()->prefix->getPrefix();
		
		foreach ([RedisKeyBuilder::getEventHandlersPrefix(), RedisKeyBuilder::getHandlerEventsPrefix()] as $mapPrefix)
		{
			$transaction->eval("return redis.call('del', 'defaultKey', unpack(redis.call('keys', ARGV[1])))", 
			0, $prefix . $mapPrefix . '*');
		}
	}
	
	private function convertEventHandlers(array $eventToHandlers): array
	{
		$handlerEvents = [];
		$eventHandlers = [];
		
		foreach ($eventToHandlers as $eventId => $handler)
		{
			if (!is_array($handler))
			{
				$eventHandlers[RedisKeyBuilder::eventHandlers($eventId)][$handler] = time();
				$handlerEvents[RedisKeyBuilder::handlerEvents($handler)][$eventId] = time();
				
				continue;
			}
			
			foreach ($handler as $handlerId)
			{
				$eventHandlers[RedisKeyBuilder::eventHandlers($eventId)][$handlerId] = time();
				$handlerEvents[RedisKeyBuilder::handlerEvents($handlerId)][$eventId] = time();
			}
		}
		
		return [
			'events' 	=> $eventHandlers,
			'handlers'	=> $handlerEvents
		];
	}
	
	private function prepareSeedNewData(MultiExec $transaction, array $data): void
	{
		foreach ($data['events'] as $eventKey => $handlers)
		{
			$transaction->hmset($eventKey, $handlers);
		}
		
		foreach ($data['handlers'] as $handlerKey => $events)
		{
			$transaction->hmset($handlerKey, $events);
		}
	}

	private function convertNamesToIds(array $eventNamesToHandlers): array
	{
		$eventIdsToHandlers = [];
		
		foreach ($eventNamesToHandlers as $eventName => $handler)
		{
			$eventId = $this->client->hget(RedisKeyBuilder::eventByName(), $eventName);
			
			if (!is_array($handler))
			{
				$handlerId = $this->client->hget(RedisKeyBuilder::handlerByName(), $handler);
				
				$eventIdsToHandlers[$eventId] = $handlerId;
				
				continue;
			}
			
			foreach ($handler as $handlerName)
			{
				$handlerId = $this->client->hget(RedisKeyBuilder::handlerByName(), $handlerName);
				$eventIdsToHandlers[$eventId][] = $handlerId;
			}
		}
		
		return $eventIdsToHandlers;
	}
	
	
	public function setClient(Client $client)
	{
		$this->client = $client;
	}

	public function subscribe(string $eventId, string $handlerId): void
	{
		$transaction = $this->client->transaction();
		
		$transaction->hset(RedisKeyBuilder::eventHandlers($eventId), $handlerId, time());
		$transaction->hset(RedisKeyBuilder::handlerEvents($handlerId), $eventId, time());
		
		$transaction->execute();
	}

	public function unsubscribe(string $eventId, string $handlerId): void
	{
		$transaction = $this->client->transaction();
		
		$transaction->hdel(RedisKeyBuilder::eventHandlers($eventId), [$handlerId]);
		$transaction->hdel(RedisKeyBuilder::handlerEvents($handlerId), [$eventId]);
		
		$transaction->execute();
	}

	public function getHandlersIds(string $eventId): array
	{
		return $this->client->hkeys(RedisKeyBuilder::eventHandlers($eventId));
	}

	public function getEventsIds(string $handlerId): array
	{
		return $this->client->hkeys(RedisKeyBuilder::handlerEvents($handlerId));
	}

	public function addSubscribers(array $eventToHandlers): void
	{
		if (!$eventToHandlers)
		{
			throw new InvalidUsageException('Passing empty array is not allowed. It will remove all subscribers.');
		}

		$data = $this->convertEventHandlers($eventToHandlers);
		
		$transaction = $this->client->transaction();
		
		$this->prepareCleanUp($transaction);
		$this->prepareSeedNewData($transaction, $data);
		
		$transaction->execute();
	}

	public function addSubscribersByNames(array $eventNamesToHandlers): void
	{
		$eventIdsToHandlers = $this->convertNamesToIds($eventNamesToHandlers);
		$this->addSubscribers($eventIdsToHandlers);
	}

	public function addExecutor(string $handlerId, string $eventId): void
	{
		$this->client->hset(RedisKeyBuilder::executorsKey($handlerId), $eventId, time());
	}
}