<?php
namespace Eddy\Base\Setup;


use Eddy\Base\IEddyQueueObject;
use Eddy\Objects\EventObject;
use Eddy\Objects\HandlerObject;


/**
 * @skeleton
 */
interface IClassNameLoader
{
	public function loadEvent(string $className): ?EventObject;
	public function loadHandler(string $className): ?HandlerObject;
	public function load(string $className): ?IEddyQueueObject;
}