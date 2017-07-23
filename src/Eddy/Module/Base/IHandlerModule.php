<?php
namespace Eddy\Module\Base;


use Eddy\Base\IEventConfig;
use Eddy\Base\IHandlerConfig;


interface IHandlerModule
{
	/**
	 * @return IHandlerConfig[]|array
	 */
	public function loadForEvent(IEventConfig $eventConfig): array;
	
	public function load(string $id): ?IHandlerConfig;
	public function loadByClass(string $class): ?IHandlerConfig;
	
	public function pause(IHandlerConfig $eventConfig): void;
	
	public function create(IHandlerConfig $eventConfig): void;
	public function update(IHandlerConfig $eventConfig): void;
	public function delete(IHandlerConfig $eventConfig): bool;
}