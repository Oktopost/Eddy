<?php
namespace Eddy\DAL\MySQL;


use Eddy\Object\EventObject;
use Eddy\Base\DAL\IEventDAO;
use Eddy\DAL\MySQL\Base\Connector\IEventConnector;

use Squid\MySql\IMySqlConnector;


/**
 * @autoload
 */
class EventDAO implements IEventDAO
{
	/** @var IEventConnector */
	private $connector;
	
	
	public function __construct(IEventConnector $connector)
	{
		$this->connector = $connector;
	}

	
	public function setConnector(IMySqlConnector $connector): IEventDAO
	{
		$this->connector->setMySQL($connector);
		return $this;
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