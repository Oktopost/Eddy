<?php
namespace Eddy\DAL\Fallback;


use Eddy\Base\IConfig;
use Eddy\Base\DAL\ISubscribersDAO;
use Eddy\DAL\Fallback\Base\IFallbackSubscribersDAO;


/**
 * @autoload
 */
class FallbackSubscribersDAO implements IFallbackSubscribersDAO
{
	/** @var IConfig */
	private $config;
	
	/** @var ISubscribersDAO */
	private $main;
	
	/** @var ISubscribersDAO */
	private $fallback;
	
	
	private function logError(\Throwable $t): void
	{
		$this->config->ExceptionHandler->exception($t);
	}
	
		
	public function setConfig(IConfig $config): void
	{
		$this->config = $config;
	}
	
	public function setMain(ISubscribersDAO $dao): void
	{
		$this->main = $dao;
	}

	public function setFallback(ISubscribersDAO $dao): void
	{
		$this->fallback = $dao;
	}

	public function subscribe(string $eventId, string $handlerId): void
	{
		$this->main->subscribe($eventId, $handlerId);
	}

	public function unsubscribe(string $eventId, string $handlerId): void
	{
		$this->main->unsubscribe($eventId, $handlerId);
	}

	public function getHandlersIds(string $eventId): array
	{
		try
		{
			return $this->main->getHandlersIds($eventId);
		}
		catch (\Throwable $e)
		{
			$this->logError($e);
			return $this->fallback->getHandlersIds($eventId);
		}
	}

	public function getEventsIds(string $handlerId): array
	{
		try
		{
			return $this->main->getEventsIds($handlerId);
		}
		catch (\Throwable $e)
		{
			$this->logError($e);
			return $this->fallback->getEventsIds($handlerId);
		}
	}

	public function addSubscribers(array $eventToHandlers): void
	{
		$this->main->addSubscribers($eventToHandlers);
	}

	public function addSubscribersByNames(array $eventNamesToHandlers): void
	{
		$this->main->addSubscribersByNames($eventNamesToHandlers);
	}

	public function addExecutor(string $handlerId, string $eventId): void
	{
		$this->main->addExecutor($handlerId, $eventId);
	}
}