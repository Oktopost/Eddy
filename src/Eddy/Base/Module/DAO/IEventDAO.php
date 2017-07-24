<?php
namespace Eddy\Module\Base\DAO;


use Eddy\Object\EventObject;

use Squid\MySql\IMySqlConnector;


interface IEventDAO
{
	/**
	 * @param array|IMySqlConnector $config
	 */
	public function initConnector($config): IEventDAO;
	
	public function load(string $eventId): ?EventObject;
	public function loadByName(string $name): ?EventObject;
	public function loadByClassName(string $className): ?EventObject;
	
	public function create(EventObject $event): void;
	public function update(EventObject $event): void;
	public function delete(EventObject $event): bool;
}