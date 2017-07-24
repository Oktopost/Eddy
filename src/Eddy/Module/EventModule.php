<?php
namespace Eddy\Module;


use Eddy\IEventConfig;
use Eddy\Object\EventObject;
use Eddy\Object\HandlerObject;
use Eddy\Base\Module\IEventModule;
use Eddy\Base\Module\DAO\IEventDAO;


/**
 * @autoload
 */
class EventModule implements IEventModule
{
	/** @var IEventDAO */
	private $dao;
	
	
	public function __construct(IEventDAO $dao)
	{
		$this->dao = $dao;
	}
	
	
	public function load(string $eventId): ?EventObject
	{
		// TODO: Implement load() method.
	}

	public function loadByName(string $name): ?EventObject
	{
		// TODO: Implement loadByName() method.
	}

	public function loadByInterfaceName(string $interfaceName): ?EventObject
	{
		// TODO: Implement loadByInterfaceName() method.
	}

	public function loadAllForHandler(HandlerObject $handler): array
	{
		// TODO: Implement loadAllForHandler() method.
	}

	public function pause(EventObject $event): void
	{
		// TODO: Implement pause() method.
	}

	public function unpause(EventObject $event): void
	{
		// TODO: Implement unpause() method.
	}

	public function save(EventObject $event): void
	{
		// TODO: Implement save() method.
	}

	public function createFromConfig(IEventConfig $eventConfig): ?EventObject
	{
		// TODO: Implement createFromConfig() method.
	}

	public function delete(EventObject $event): bool
	{
		// TODO: Implement delete() method.
	}
}