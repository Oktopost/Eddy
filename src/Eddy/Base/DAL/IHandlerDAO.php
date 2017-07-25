<?php
namespace Eddy\Base\DAL;


use Eddy\Object\EventObject;
use Eddy\Object\HandlerObject;

use Squid\MySql\IMySqlConnector;


/**
 * @skeleton
 */
interface IHandlerDAO
{
	public function setConnector(IMySqlConnector $connector): IHandlerDAO;
	
	public function load(string $id): ?HandlerObject;
	public function loadMultiple(array $ids): array;
	public function loadByClassName(string $className): ?HandlerObject;
	
	public function create(HandlerObject $handler): bool;
	public function update(HandlerObject $handler): bool;
	public function delete(HandlerObject $handler): bool;
}