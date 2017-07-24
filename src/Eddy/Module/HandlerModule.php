<?php
namespace Eddy\Module;


use Eddy\IHandlerConfig;
use Eddy\Object\EventObject;
use Eddy\Object\HandlerObject;
use Eddy\Base\Module\IHandlerModule;
use Eddy\Base\Module\DAO\IHandlerDAO;


/**
 * @autoload
 */
class HandlerModule implements IHandlerModule
{
	/** @var IHandlerDAO */
	private $dao;
	
	
	public function __construct(IHandlerDAO $dao)
	{
		$this->dao = $dao;
	}
	
	
	public function load(string $id): ?HandlerObject
	{
		// TODO: Implement load() method.
	}

	public function loadByClassName(string $className): ?HandlerObject
	{
		// TODO: Implement loadByClassName() method.
	}

	public function loadAllForEvent(EventObject $event): array
	{
		// TODO: Implement loadAllForEvent() method.
	}

	public function pause(EventObject $event): void
	{
		// TODO: Implement pause() method.
	}

	public function unpause(EventObject $event): void
	{
		// TODO: Implement unpause() method.
	}

	public function createFromConfig(IHandlerConfig $handlerConfig): ?HandlerObject
	{
		// TODO: Implement createFromConfig() method.
	}

	public function save(HandlerObject $handler): void
	{
		// TODO: Implement save() method.
	}

	public function delete(HandlerObject $handler): bool
	{
		// TODO: Implement delete() method.
	}
}