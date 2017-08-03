<?php
namespace Eddy\Base\DAL;


use Eddy\Object\EventObject;

use Squid\MySql\IMySqlConnector;


/**
 * @skeleton
 */
interface IEventDAO
{
	public function setConnector(IMySqlConnector $connector): IEventDAO;
	
	public function load(string $eventId): ?EventObject;
	public function loadMultiple(array $ids): array;
	public function loadByIdentifier(string $identifier): ?EventObject;
	
	public function loadByName(string $name): ?EventObject;
	public function loadByInterfaceName(string $interfaceName): ?EventObject;
	
	public function saveSetup(EventObject $event): bool;

	/**
	 * @param EventObject[]|array $events
	 * @return bool
	 */
	public function saveSetupAll(array $events): bool;
	
	public function updateSettings(EventObject $event): bool;
	
	public function delete(EventObject $event): bool;
}