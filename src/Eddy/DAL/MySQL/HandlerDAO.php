<?php
namespace Eddy\Module\DAO;


use Eddy\Base\DAL\IEventDAO;
use Eddy\Object\EventObject;
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

	public function loadForEvent(EventObject $event): array
	{
		// TODO: Implement loadForEvent() method.
	}

	public function load(string $id): ?HandlerObject
	{
		// TODO: Implement load() method.
	}

	public function loadByClassName(string $class): ?HandlerObject
	{
		// TODO: Implement loadByClassName() method.
	}

	public function create(HandlerObject $handler): void
	{
		// TODO: Implement create() method.
	}

	public function update(HandlerObject $handler): void
	{
		// TODO: Implement update() method.
	}

	public function delete(HandlerObject $handler): bool
	{
		// TODO: Implement delete() method.
	}
}