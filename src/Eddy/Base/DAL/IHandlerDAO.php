<?php
namespace Eddy\Base\DAL;


use Eddy\Object\EventObject;
use Eddy\Object\HandlerObject;

use Squid\MySql\IMySqlConnector;


/**
 * @skeleton
 */
interface IHandlerDAO
{
	public function setConnector(IMySqlConnector $connector): IHandlerDAO;
	
	public function load(string $id): ?HandlerObject;
	public function loadMultiple(array $ids): array;
	public function loadByIdentifier(string $identifier): ?HandlerObject;
	
	public function loadByName(string $name): ?HandlerObject;
	public function loadByClassName(string $className): ?HandlerObject;
	
	public function saveSetup(HandlerObject $handler): bool;
	
	/**
	 * @param HandlerObject[]|array $events
	 * @return bool
	 */
	public function saveSetupAll(array $handlers): bool;
	
	public function updateSettings(HandlerObject $handler): bool;
	
	public function delete(HandlerObject $handler): bool;
}