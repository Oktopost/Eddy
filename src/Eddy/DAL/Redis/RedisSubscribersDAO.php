<?php
namespace Eddy\DAL\Redis;


use Eddy\DAL\Redis\Base\IRedisSubscribersDAO;

use Eddy\Exceptions\InvalidUsageException;
use Predis\Client;
use Predis\Transaction\MultiExec;


class RedisSubscribersDAO implements IRedisSubscribersDAO
{
	private const EVENT_HANDLERS_PREFIX	= 'EventHandlers:';
	private const HANDLER_EVENTS_PREFIX	= 'HandlerEvents:';
	
	private const EVENT_BY_NAME_KEY		= 'EventObjectsByName';
	private const HANDLER_BY_NAME_KEY	= 'HandlerObjectsByName';
	private const EXECUTORS_KEY			= 'EventExecutors';
	
	
	/** @var Client */
	private $client;
	
	
	private function getEventKey(string $eventId): string 
	{
		return self::EVENT_HANDLERS_PREFIX . $eventId;
	}
	
	private function getHandlerKey(string $handlerId): string 
	{
		return self::HANDLER_EVENTS_PREFIX . $handlerId;
	}
	
	private function prepareCleanUp(MultiExec $transaction): void
	{
		$prefix = $this->client->getOptions()->prefix->getPrefix();
		
		foreach ([self::EVENT_HANDLERS_PREFIX, self::HANDLER_EVENTS_PREFIX] as $mapPrefix)
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
				$eventHandlers[$this->getEventKey($eventId)][$handler] = time();
				$handlerEvents[$this->getHandlerKey($handler)][$eventId] = time();
				
				continue;
			}
			
			foreach ($handler as $handlerId)
			{
				$eventHandlers[$this->getEventKey($eventId)][$handlerId] = time();
				$handlerEvents[$this->getHandlerKey($handlerId)][$eventId] = time();
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
			$eventId = $this->client->hget(self::EVENT_BY_NAME_KEY, $eventName);
			
			if (!is_array($handler))
			{
				$handlerId = $this->client->hget(self::HANDLER_BY_NAME_KEY, $handler);
				
				$eventIdsToHandlers[$eventId] = $handlerId;
				
				continue;
			}
			
			foreach ($handler as $handlerName)
			{
				$handlerId = $this->client->hget(self::HANDLER_BY_NAME_KEY, $handlerName);
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
		
		$transaction->hset($this->getEventKey($eventId), $handlerId, time());
		$transaction->hset($this->getHandlerKey($handlerId), $eventId, time());
		
		$transaction->execute();
	}

	public function unsubscribe(string $eventId, string $handlerId): void
	{
		$transaction = $this->client->transaction();
		
		$transaction->hdel($this->getEventKey($eventId), [$handlerId]);
		$transaction->hdel($this->getHandlerKey($handlerId), [$eventId]);
		
		$transaction->execute();
	}

	public function getHandlersIds(string $eventId): array
	{
		return $this->client->hkeys($this->getEventKey($eventId));
	}

	public function getEventsIds(string $handlerId): array
	{
		return $this->client->hkeys($this->getHandlerKey($handlerId));
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
		// TODO: Implement addExecutor() method.
	}
}