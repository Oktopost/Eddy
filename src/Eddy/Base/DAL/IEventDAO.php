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
	public function loadByName(string $name): ?EventObject;
	public function loadByClassName(string $className): ?EventObject;
	
	public function create(EventObject $event): void;
	public function update(EventObject $event): void;
	public function delete(EventObject $event): bool;
}