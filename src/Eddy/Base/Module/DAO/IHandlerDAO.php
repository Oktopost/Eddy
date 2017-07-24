<?php
namespace Eddy\Module\Base\DAO;


use Eddy\Object\EventObject;
use Eddy\Object\HandlerObject;

use Squid\MySql\IMySqlConnector;


interface IHandlerDAO
{
	/**
	 * @param array|IMySqlConnector $config
	 */
	public function initConnector($config): IEventDAO;
	
	public function loadForEvent(EventObject $event): array;
	public function load(string $id): ?HandlerObject;
	public function loadByClassName(string $class): ?HandlerObject;
	
	public function create(HandlerObject $handler): void;
	public function update(HandlerObject $handler): void;
	public function delete(HandlerObject $handler): bool;
}