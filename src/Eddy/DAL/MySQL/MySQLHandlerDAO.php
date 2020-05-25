<?php
namespace Eddy\DAL\MySQL;


use Eddy\Enums\EventState;
use Eddy\Objects\HandlerObject;
use Eddy\DAL\MySQL\Base\IMySQLHandlerDAO;
use Eddy\DAL\MySQL\Base\Connector\IHandlerConnector;

use DeepQueue\Utils\TimeBasedRandomIdGenerator;

use Squid\MySql\IMySqlConnector;


/**
 * @autoload
 */
class MySQLHandlerDAO implements IMySQLHandlerDAO
{
	/** @var IHandlerConnector */
	private $connector;
	
	
	public function __construct(IHandlerConnector $connector)
	{
		$this->connector = $connector;
	}

	
	public function setConnector(IMySqlConnector $connector): IMySQLHandlerDAO
	{
		$this->connector->setMySQL($connector);
		return $this;
	}

	public function load(string $handlerId): ?HandlerObject
	{
		return $this->connector->selectFirstObjectByFields([
			'Id'	=> $handlerId,
			'State'	=> EventState::EXISTING
		]);
	}
	
	public function loadMultiple(array $ids): array
	{
		$objects = $this->connector->selectObjectsByFields(['Id' => $ids]);
		return $objects ?: [];
	}
	
	public function loadAllRunning(): array
	{
		$objects = $this->connector->selectObjectsByFields(['State' => EventState::RUNNING]);
		
		return $objects ?: [];
	}
	
	public function loadByIdentifier(string $identifier): ?HandlerObject 
	{
		$handler = $this->loadByClassName($identifier);
		
		if (!$handler)
		{
			$handler = $this->loadByName($identifier);
		}
		
		return $handler;
	}
	
	public function loadByName(string $name): ?HandlerObject
	{
		return $this->connector->selectFirstObjectByFields([
			'Name'	=> $name,
			'State'	=> EventState::EXISTING
		]);
	}

	public function loadByClassName(string $className): ?HandlerObject
	{
		return $this->connector->selectFirstObjectByFields([
			'HandlerClassName'	=> $className,
			'State'	=> EventState::EXISTING
		]);
	}

	public function saveSetup(HandlerObject $handler): bool
	{
		return $this->saveSetupAll([$handler]);
	}

	public function saveSetupAll(array $handlers): bool
	{
		foreach ($handlers as $handler)
		{
			if (!$handler->Id)
			{
				$handler->Id = (new TimeBasedRandomIdGenerator())->get();
			}
		}
		
		return $this->connector->upsertObjectsForValues($handlers, [
			'HandlerClassName',
			'ConfigClassName'
		]);
	}

	public function updateSettings(HandlerObject $handler): bool
	{
		if (!$handler->Id) return false;
		
		return $this->connector->upsertObjectsForValues([$handler], [
			'State',
			'Delay',
			'MaxBulkSize',
			'DelayBuffer',
			'PackageSize'
		]);
	}

	public function delete(HandlerObject $handler): bool
	{
		$handler->State = EventState::DELETED;
		return $this->connector->save($handler);
	}
}