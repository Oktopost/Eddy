<?php
namespace Eddy\DAL\MySQL;


use Eddy\Enums\EventState;
use Eddy\Object\EventObject;
use Eddy\DAL\MySQL\Base\IMySQLEventDAO;
use Eddy\DAL\MySQL\Base\Connector\IEventConnector;

use DeepQueue\Utils\TimeBasedRandomIdGenerator;

use Squid\MySql\IMySqlConnector;


/**
 * @autoload
 */
class MySQLEventDAO implements IMySQLEventDAO
{
	/** @var IEventConnector */
	private $connector;
	
	
	public function __construct(IEventConnector $connector)
	{
		$this->connector = $connector;
	}

	
	public function setConnector(IMySqlConnector $connector): IMySQLEventDAO
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
		$objects = $this->connector->selectObjectsByFields(['Id' => $ids]);
		return $objects ?: [];
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

	public function saveSetup(EventObject $event): bool
	{
		return $this->saveSetupAll([$event]);
	}

	public function saveSetupAll(array $events): bool
	{
		foreach ($events as $event)
		{
			if (!$event->Id)
			{
				$event->Id = (new TimeBasedRandomIdGenerator())->get();
			}
		}
		
		return $this->connector->upsertObjectsForValues($events, [
			'EventInterface',
			'ProxyClassName',
			'ConfigClassName',
			'HandlerInterface'
		]);
	}

	public function updateSettings(EventObject $event): bool
	{
		if (!$event->Id) return false;
		
		return $this->connector->upsertObjectsForValues([$event], [
			'State',
			'Delay',
			'MaxBulkSize'
		]);
	}

	public function delete(EventObject $event): bool
	{
		$event->State = EventState::DELETED;
		return $this->connector->save($event);
	}
}