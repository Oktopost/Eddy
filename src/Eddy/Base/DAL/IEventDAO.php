<?php
namespace Eddy\Base\DAL;


use Eddy\Objects\EventObject;


/**
 * @skeleton
 */
interface IEventDAO
{
	public function load(string $eventId): ?EventObject;
	public function loadByIdentifier(string $identifier): ?EventObject;
	public function loadByName(string $name): ?EventObject;
	public function loadByInterfaceName(string $interfaceName): ?EventObject;

	/**
	 * @return EventObject[]|array
	 */
	public function loadMultiple(array $ids): array;
	
	/**
	 * @return EventObject[]|array
	 */
	public function loadAllRunning(): array;
	
	public function saveSetup(EventObject $event): bool;

	/**
	 * @param EventObject[]|array $events
	 * @return bool
	 */
	public function saveSetupAll(array $events): bool;
	public function updateSettings(EventObject $event): bool;
	public function delete(EventObject $event): bool;
}