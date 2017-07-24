<?php
namespace Eddy\DAO;


use Eddy\Object\EventObject;
use Eddy\Module\Base\DAO\IEventDAO;

use Squid\MySql\IMySqlConnector;


class EventDAO implements IEventDAO
{
	/**
	 * @param array|IMySqlConnector $config
	 */
	public function initConnector($config): IEventDAO
	{
		// TODO: Implement initConnector() method.
	}

	public function load(string $eventId): ?EventObject
	{
		// TODO: Implement load() method.
	}

	public function loadByName(string $name): ?EventObject
	{
		// TODO: Implement loadByName() method.
	}

	public function loadByClassName(string $className): ?EventObject
	{
		// TODO: Implement loadByClassName() method.
	}

	public function create(EventObject $event): void
	{
		// TODO: Implement create() method.
	}

	public function update(EventObject $event): void
	{
		// TODO: Implement update() method.
	}

	public function delete(EventObject $event): bool
	{
		// TODO: Implement delete() method.
	}
}