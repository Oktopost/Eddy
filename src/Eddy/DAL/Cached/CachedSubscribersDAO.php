<?php
namespace Eddy\DAL\Cached;


use Eddy\Base\DAL\ICacheDAO;
use Eddy\Base\DAL\ISubscribersDAO;
use Eddy\DAL\Cached\Base\ICachedSubscribersDAO;


class CachedSubscribersDAO implements ICachedSubscribersDAO
{
	public function setMain(ISubscribersDAO $dao): void
	{
		// TODO: Implement setMain() method.
	}

	public function setCache(ICacheDAO $dao): void
	{
		// TODO: Implement setCache() method.
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