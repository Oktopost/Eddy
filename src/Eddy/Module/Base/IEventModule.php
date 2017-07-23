<?php
namespace Eddy\Module\Base;


use Eddy\Base\IEventConfig;


interface IEventModule
{
	public function load(string $eventId): ?IEventConfig;
	public function loadByName(string $name): ?IEventConfig;
	public function loadByInterface(string $interface): ?IEventConfig;
	
	public function pause(IEventConfig $eventConfig): void;
	
	public function create(IEventConfig $eventConfig): void;
	public function update(IEventConfig $eventConfig): void;
	public function delete(IEventConfig $eventConfig): bool;
}