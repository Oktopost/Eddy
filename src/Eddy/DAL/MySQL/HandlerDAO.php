<?php
namespace Eddy\DAL\MySQL;


use Eddy\Enums\EventState;
use Eddy\Object\HandlerObject;
use Eddy\Base\DAL\IHandlerDAO;
use Eddy\DAL\MySQL\Base\Connector\IHandlerConnector;

use Squid\MySql\IMySqlConnector;


/**
 * @autoload
 */
class HandlerDAO implements IHandlerDAO
{
	/** @var IHandlerConnector */
	private $connector;
	
	
	public function __construct(IHandlerConnector $connector)
	{
		$this->connector = $connector;
	}

	
	public function setConnector(IMySqlConnector $connector): IHandlerDAO
	{
		$this->connector->setMySQL($connector);
		return $this;
	}

	public function load(string $eventId): ?HandlerObject
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

	public function save(HandlerObject $event): bool
	{
		return $this->connector->save($event);
	}

	public function delete(HandlerObject $handler): bool
	{
		$handler->State = EventState::DELETED;
		return $this->save($handler);
	}
}