<?php
namespace Eddy\Base\Setup;


use Eddy\Base\IEddyQueueObject;
use Eddy\Object\EventObject;
use Eddy\Object\HandlerObject;


/**
 * @skeleton
 */
interface IClassNameLoader
{
	public function loadEvent(string $className): ?EventObject;
	public function loadHandler(string $className): ?HandlerObject;
	public function load(string $className): ?IEddyQueueObject;
}