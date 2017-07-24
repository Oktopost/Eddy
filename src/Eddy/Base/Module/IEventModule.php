<?php
namespace Eddy\Module\Base;


use Eddy\IEventConfig;
use Eddy\Object\EventObject;
use Eddy\Object\HandlerObject;


interface IEventModule
{
	public function load(string $eventId): ?EventObject;
	public function loadByName(string $name): ?EventObject;
	public function loadByInterfaceName(string $interfaceName): ?EventObject;
	
	public function loadAllForHandler(HandlerObject $handler): array;
	
	public function pause(EventObject $event): void;
	public function unpause(EventObject $event): void;
	
	public function save(EventObject $event): void;
	public function createFromConfig(IEventConfig $eventConfig): ?EventObject;
	public function delete(EventObject $event): bool;
}