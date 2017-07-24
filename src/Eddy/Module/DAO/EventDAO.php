<?php
namespace Eddy\Module\DAO;


use Eddy\Object\EventObject;
use Eddy\Base\Module\DAO\IEventDAO;
use Eddy\Base\Module\DAO\Connector\IEventConnector;

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


	/**
	 * @param array|IMySqlConnector $config
	 */
	public function initConnector($config): IEventDAO
	{
		if (is_array($config))
		{
			$sql = \Squid::MySql();
			$sql->config()->setConfig($config);
			
			$config = $sql->getConnector();
		}
		
		$this->connector->setMySQL($config);
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