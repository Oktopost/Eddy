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

	public function loadByClassName(string $className): ?HandlerObject
	{
		return $this->connector->selectFirstObjectByFields([
			'HandlerClassName'	=> $className,
			'State'	=> EventState::EXISTING
		]);
	}

	public function create(HandlerObject $handler): bool
	{
		return $this->connector->insert($handler);
	}

	public function update(HandlerObject $handler): bool
	{
		return $this->connector->update($handler);
	}

	public function delete(HandlerObject $handler): bool
	{
		$handler->State = EventState::DELETED;
		return $this->update($handler);
	}
}