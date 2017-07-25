<?php
namespace Eddy\DAL\MySQL;


use Eddy\Enums\EventState;
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
		return $this->connector->selectFirstObjectByFields([
			'Id'	=> $eventId,
			'State'	=> EventState::EXISTING
		]);
	}

	public function loadMultiple(array $ids): array
	{
		return $this->connector->selectObjectsByFields(['Id' => $ids]);
	}
	
	public function loadByIdentifier(string $identifier): ?EventObject
	{
		$event = $this->loadByInterfaceName($identifier);
		
		if (!$event)
		{
			$event = $this->loadByName($identifier);
		}
		
		return $event;
	}

	public function loadByName(string $name): ?EventObject
	{
		return $this->connector->selectFirstObjectByFields([
			'Name'	=> $name,
			'State'	=> EventState::EXISTING
		]);
	}

	public function loadByInterfaceName(string $interfaceName): ?EventObject
	{
		return $this->connector->selectFirstObjectByFields([
			'EventInterface'	=> $interfaceName,
			'State'	=> EventState::EXISTING
		]);
	}

	public function create(EventObject $event): bool
	{
		return $this->connector->insert($event);
	}

	public function update(EventObject $event): bool
	{
		return $this->connector->update($event);
	}

	public function delete(EventObject $event): bool
	{
		$event->State = EventState::DELETED;
		return $this->update($event);
	}
}