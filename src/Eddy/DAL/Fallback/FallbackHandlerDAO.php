<?php
namespace Eddy\DAL\Fallback;


use Eddy\Base\IConfig;
use Eddy\Object\HandlerObject;
use Eddy\Base\DAL\IHandlerDAO;
use Eddy\DAL\Fallback\Base\IFallbackHandlerDAO;


/**
 * @autoload
 */
class FallbackHandlerDAO implements IFallbackHandlerDAO
{
	/** @var IConfig */
	private $config;
	
	/** @var IHandlerDAO */
	private $main;
	
	/** @var IHandlerDAO */
	private $fallback;
	
	
	private function logError(\Throwable $t): void
	{
		$this->config->ExceptionHandler->exception($t);
	}
	
	
	public function setConfig(IConfig $config): void
	{
		$this->config = $config;
	}
	
	public function setMain(IHandlerDAO $dao): void
	{
		$this->main = $dao;
	}

	public function setFallback(IHandlerDAO $dao): void
	{
		$this->fallback = $dao;
	}

	public function load(string $id): ?HandlerObject
	{
		try
		{
			return $this->main->load($id);
		}
		catch (\Throwable $e)
		{
			$this->logError($e);
			return $this->fallback->load($id);
		}
	}

	public function loadByIdentifier(string $identifier): ?HandlerObject
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

	public function loadByName(string $name): ?HandlerObject
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

	public function loadByClassName(string $className): ?HandlerObject
	{
		try
		{
			return $this->main->loadByClassName($className);
		}
		catch (\Throwable $e)
		{
			$this->logError($e);
			return $this->fallback->loadByClassName($className);
		}
	}

	/**
	 * @return HandlerObject[]|array
	 */
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

	/**
	 * @return HandlerObject[]|array
	 */
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

	public function saveSetup(HandlerObject $handler): bool
	{
		return $this->main->saveSetup($handler);
	}

	/**
	 * @param HandlerObject[]|array $handlers
	 * @return bool
	 */
	public function saveSetupAll(array $handlers): bool
	{
		return $this->main->saveSetupAll($handlers);
	}

	public function updateSettings(HandlerObject $handler): bool
	{
		return $this->main->updateSettings($handler);
	}

	public function delete(HandlerObject $handler): bool
	{
		return $this->main->delete($handler);
	}
}