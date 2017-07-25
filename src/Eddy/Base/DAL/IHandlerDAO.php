<?php
namespace Eddy\Base\Module\DAO;


use Eddy\Object\EventObject;
use Eddy\Object\HandlerObject;

use Squid\MySql\IMySqlConnector;


/**
 * @skeleton
 */
interface IHandlerDAO
{
	/**
	 * @param array|IMySqlConnector $config
	 */
	public function initConnector($config): IHandlerDAO;
	
	public function loadForEvent(EventObject $event): array;
	public function load(string $id): ?HandlerObject;
	public function loadByClassName(string $class): ?HandlerObject;
	
	public function create(HandlerObject $handler): void;
	public function update(HandlerObject $handler): void;
	public function delete(HandlerObject $handler): bool;
}