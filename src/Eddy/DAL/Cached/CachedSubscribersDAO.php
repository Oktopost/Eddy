<?php
namespace Eddy\DAL\Cached;


use Eddy\Base\DAL\ICacheDAO;
use Eddy\Base\DAL\ISubscribersDAO;
use Eddy\DAL\Cached\Base\ICachedSubscribersDAO;
use Eddy\Exceptions\InvalidUsageException;


class CachedSubscribersDAO implements ICachedSubscribersDAO
{
	/** @var ISubscribersDAO */
	private $main;
	
	/** @var ISubscribersDAO|ICacheDAO */
	private $cache;
	
	
	public function setMain(ISubscribersDAO $dao): void
	{
		$this->main = $dao;
	}

	public function setCache(ICacheDAO $dao): void
	{
		if (!$dao instanceof ISubscribersDAO)
		{
			throw new InvalidUsageException('Cache DAO must impelement ISubscribersDAO interface');
		}
		
		$this->cache = $dao;
	}

	public function subscribe(string $eventId, string $handlerId): void
	{
		$this->cache->flushAll();
		$this->main->subscribe($eventId, $handlerId);
	}

	public function unsubscribe(string $eventId, string $handlerId): void
	{
		$this->cache->flushAll();
		$this->main->unsubscribe($eventId, $handlerId);
	}

	public function getHandlersIds(string $eventId): array
	{
		$handlerIds = $this->cache->getHandlersIds($eventId);
		
		if (!$handlerIds)
		{
			$handlerIds = $this->main->getHandlersIds($eventId);
		}
		
		return $handlerIds;
	}

	public function getEventsIds(string $handlerId): array
	{
		$eventsIds = $this->cache->getEventsIds($handlerId);
		
		if (!$eventsIds)
		{
			$eventsIds = $this->main->getEventsIds($handlerId);
		}
		
		return $eventsIds;
	}

	public function addSubscribers(array $eventToHandlers): void
	{
		$this->main->addSubscribers($eventToHandlers);
		$this->cache->flushAll();
	}

	public function addSubscribersByNames(array $eventNamesToHandlers): void
	{
		$this->main->addSubscribersByNames($eventNamesToHandlers);
		$this->cache->flushAll();
	}

	public function addExecutor(string $handlerId, string $eventId): void
	{
		$this->main->addExecutor($handlerId, $eventId);
	}
	
	public function flushAll(): void
	{
		$this->cache->flushAll();
	}
}