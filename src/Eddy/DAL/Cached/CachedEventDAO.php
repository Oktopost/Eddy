<?php
namespace Eddy\DAL\Cached;


use Eddy\Base\DAL\ICacheDAO;
use Eddy\Base\DAL\IEventDAO;
use Eddy\Object\EventObject;
use Eddy\DAL\Cached\Base\ICachedEventDAO;
use Eddy\Exceptions\InvalidUsageException;


class CachedEventDAO implements ICachedEventDAO
{
	/** @var IEventDAO */
	private $main;
	
	/** @var IEventDAO|ICacheDAO */
	private $cache;
	
	
	private function loadAndCacheAdditional(array $ids, array $events): array 
	{
		$notLoaded = $ids;
		
		if ($events)
		{
			$mapper = function ($o) { return $o->Id; };

			$loadedIds = array_map($mapper, $events);
			$notLoaded = array_diff($ids, $loadedIds);
		}
		
		$additionalEvents = $this->main->loadMultiple($notLoaded);
		
		if ($additionalEvents)
		{
			$this->cache->saveSetupAll($additionalEvents);
		}
		
		return array_merge($events, $additionalEvents);
	}
	
	
	public function setMain(IEventDAO $dao): void
	{
		$this->main = $dao;
	}

	public function setCache(ICacheDAO $dao): void
	{
		if (!$dao instanceof IEventDAO)
		{
			throw new InvalidUsageException('Cache DAO must impelement IEventDAO interface');
		}
		
		$this->cache = $dao;
	}

	public function load(string $eventId): ?EventObject
	{
		$event = $this->cache->load($eventId);
		
		if (!$event)
		{
			$event = $this->main->load($eventId);
			
			if ($event)
			{
				$this->cache->saveSetup($event);
			}
		}
		
		return $event;
	}

	public function loadMultiple(array $ids): array
	{
		$events = $this->cache->loadMultiple($ids);
		
		if (count($events) < count($ids))
		{
			$events = $this->loadAndCacheAdditional($ids, $events);
		}
		
		return $events;
	}

	public function loadAllRunning(): array
	{
		return $this->main->loadAllRunning();
	}


	public function loadByIdentifier(string $identifier): ?EventObject
	{
		$event = $this->cache->loadByIdentifier($identifier);
		
		if (!$event)
		{
			$event = $this->main->loadByIdentifier($identifier);
			
			if ($event)
			{
				$this->cache->saveSetup($event);
			}
		}
		
		return $event;
	}

	public function loadByName(string $name): ?EventObject
	{
		$event = $this->cache->loadByName($name);
		
		if (!$event)
		{
			$event = $this->main->loadByName($name);
			
			if ($event)
			{
				$this->cache->saveSetup($event);
			}
		}
		
		return $event;
	}

	public function loadByInterfaceName(string $interfaceName): ?EventObject
	{
		$event = $this->cache->loadByInterfaceName($interfaceName);
		
		if (!$event)
		{
			$event = $this->main->loadByInterfaceName($interfaceName);
			
			if ($event)
			{
				$this->cache->saveSetup($event);
			}
		}
		
		return $event;
	}

	public function saveSetup(EventObject $event): bool
	{
		if ($event->Id)
		{
			$this->cache->delete($event);	
		}
		
		return $this->main->saveSetup($event);
	}

	public function saveSetupAll(array $events): bool
	{
		$this->cache->flushAll();
		return $this->main->saveSetupAll($events);
	}

	public function updateSettings(EventObject $event): bool
	{
		if ($event->Id)
		{
			$this->cache->delete($event);	
		}
		
		return $this->main->updateSettings($event);
	}

	public function delete(EventObject $event): bool
	{
		$this->cache->delete($event);
		return $this->main->delete($event);
	}

	public function flushAll(): void
	{
		$this->cache->flushAll();
	}
}