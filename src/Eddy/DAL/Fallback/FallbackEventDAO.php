<?php
namespace Eddy\DAL\Fallback;


use Eddy\Base\IConfig;
use Eddy\Base\DAL\IEventDAO;
use Eddy\Object\EventObject;
use Eddy\DAL\Fallback\Base\IFallbackEventDAO;


class FallbackEventDAO implements IFallbackEventDAO
{
	/** @var IConfig */
	private $config;
	
	/** @var IEventDAO */
	private $main;
	
	/** @var IEventDAO */
	private $fallback;
	
	
	private function logError(\Throwable $t): void
	{
		$this->config->ExceptionHandler->exception($t);
	}

	
	public function setConfig(IConfig $config): void
	{
		$this->config = $config;
	}

	public function setMain(IEventDAO $dao): void
	{
		$this->main = $dao;
	}

	public function setFallback(IEventDAO $dao): void
	{
		$this->fallback = $dao;
	}

	public function load(string $eventId): ?EventObject
	{
		try
		{
			return $this->main->load($eventId);
		}
		catch (\Throwable $e)
		{
			$this->logError($e);
			return $this->fallback->load($eventId);
		}
	}

	public function loadByIdentifier(string $identifier): ?EventObject
	{
		try
		{
			return $this->main->loadByIdentifier($identifier);
		}
		catch (\Throwable $e)
		{
			$this->logError($e);
			return $this->fallback->loadByIdentifier($identifier);
		}
	}

	public function loadByName(string $name): ?EventObject
	{
		try
		{
			return $this->main->loadByName($name);
		}
		catch (\Throwable $e)
		{
			$this->logError($e);
			return $this->fallback->loadByName($name);
		}
	}

	public function loadByInterfaceName(string $interfaceName): ?EventObject
	{
		try
		{
			return $this->main->loadByInterfaceName($interfaceName);
		}
		catch (\Throwable $e)
		{
			$this->logError($e);
			return $this->fallback->loadByInterfaceName($interfaceName);
		}
	}

	public function loadMultiple(array $ids): array
	{
		try
		{
			return $this->main->loadMultiple($ids);
		}
		catch (\Throwable $e)
		{
			$this->logError($e);
			return $this->fallback->loadMultiple($ids);
		}
	}

	public function loadAllRunning(): array
	{
		try
		{
			return $this->main->loadAllRunning();
		}
		catch (\Throwable $e)
		{
			$this->logError($e);
			return $this->fallback->loadAllRunning();
		}
	}

	public function saveSetup(EventObject $event): bool
	{
		return $this->main->saveSetup($event);
	}

	public function saveSetupAll(array $events): bool
	{
		return $this->main->saveSetupAll($events);
	}

	public function updateSettings(EventObject $event): bool
	{
		return $this->main->updateSettings($event);
	}
	public function delete(EventObject $event): bool
	{
		return $this->main->delete($event);
	}
}