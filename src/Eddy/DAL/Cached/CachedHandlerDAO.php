<?php
namespace Eddy\DAL\Cached;


use Eddy\Base\DAL\ICacheDAO;
use Eddy\Base\DAL\IHandlerDAO;
use Eddy\Object\HandlerObject;
use Eddy\Exceptions\InvalidUsageException;
use Eddy\DAL\Cached\Base\ICachedHandlerDAO;


class CachedHandlerDAO implements ICachedHandlerDAO
{
	/** @var IHandlerDAO */
	private $main;
	
	/** @var IHandlerDAO|ICacheDAO */
	private $cache;
	
	
	private function loadAndCacheAdditional(array $ids, array $handlers): array 
	{
		$notLoaded = $ids;
		
		if ($handlers)
		{
			$mapper = function ($o) { return $o->Id; };

			$loadedIds = array_map($mapper, $handlers);
			$notLoaded = array_diff($ids, $loadedIds);
		}
		
		$additionalEvents = $this->main->loadMultiple($notLoaded);
		
		if ($additionalEvents)
		{
			$this->cache->saveSetupAll($additionalEvents);
		}
		
		return array_merge($handlers, $additionalEvents);
	}
	
	public function setMain(IHandlerDAO $dao): void
	{
		$this->main = $dao;
	}

	public function setCache(ICacheDAO $dao): void
	{
		if (!$dao instanceof IHandlerDAO)
		{
			throw new InvalidUsageException('Cache DAO must impelement IHandlerDAO interface');
		}
		
		$this->cache = $dao;
	}

	public function load(string $id): ?HandlerObject
	{
		$handler = $this->cache->load($id);
		
		if (!$handler)
		{
			$handler = $this->main->load($id);
			
			if ($handler)
			{
				$this->cache->saveSetup($handler);
			}
		}
		
		return $handler;
	}

	public function loadMultiple(array $ids): array
	{
		$handlers = $this->cache->loadMultiple($ids);
		
		if (count($handlers) < count($ids))
		{
			$handlers = $this->loadAndCacheAdditional($ids, $handlers);
		}
		
		return $handlers;
	}

	public function loadByIdentifier(string $identifier): ?HandlerObject
	{
		$handler = $this->cache->loadByIdentifier($identifier);
		
		if (!$handler)
		{
			$handler = $this->main->loadByIdentifier($identifier);
			
			if ($handler)
			{
				$this->cache->saveSetup($handler);
			}
		}
		
		return $handler;
	}

	public function loadByName(string $name): ?HandlerObject
	{
		$handler = $this->cache->loadByName($name);
		
		if (!$handler)
		{
			$handler = $this->main->loadByName($name);
			
			if ($handler)
			{
				$this->cache->saveSetup($handler);
			}
		}
		
		return $handler;
	}

	public function loadByClassName(string $className): ?HandlerObject
	{
		$handler = $this->cache->loadByClassName($className);
		
		if (!$handler)
		{
			$handler = $this->main->loadByClassName($className);
			
			if ($handler)
			{
				$this->cache->saveSetup($handler);
			}
		}
		
		return $handler;
	}

	public function saveSetup(HandlerObject $handler): bool
	{
		if ($handler->Id)
		{
			$this->cache->delete($handler);	
		}
		
		return $this->main->saveSetup($handler);
	}

	public function saveSetupAll(array $handlers): bool
	{
		$this->cache->flushAll();
		return $this->main->saveSetupAll($handlers);
	}

	public function updateSettings(HandlerObject $handler): bool
	{
		if ($handler->Id)
		{
			$this->cache->delete($handler);	
		}
		
		return $this->main->updateSettings($handler);
	}

	public function delete(HandlerObject $handler): bool
	{
		$this->cache->delete($handler);
		return $this->main->delete($handler);
	}
	
	public function flushAll(): void
	{
		$this->cache->flushAll();
	}
}