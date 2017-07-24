<?php
namespace Eddy\Base\Module;


use Eddy\IHandlerConfig;
use Eddy\Object\EventObject;
use Eddy\Object\HandlerObject;


/**
 * @skeleton
 */
interface IHandlerModule
{
	public function load(string $id): ?HandlerObject;
	public function loadByClassName(string $className): ?HandlerObject;

	public function loadAllForEvent(EventObject $event): array;

	public function pause(EventObject $event): void;
	public function unpause(EventObject $event): void;

	public function createFromConfig(IHandlerConfig $handlerConfig): ?HandlerObject;
	public function save(HandlerObject $handler): void;
	public function delete(HandlerObject $handler): bool;
}