<?php
namespace Eddy\Base\DAL;


/**
 * @skeleton
 */
interface ISubscribersDAO
{
	public function subscribe(string $eventId, string $handlerId): void;
	public function unsubscribe(string $eventId, string $handlerId): void;
	
	public function getHandlersIds(string $eventId): array;
	public function getEventsIds(string $handlerId): array; 
	
	public function addSubscribers(array $eventToHandlers): void;
	public function addSubscribersByNames(array $eventNamesToHandlers): void;
	
	public function addExecutor(string $handlerId, string $eventId): void;
}