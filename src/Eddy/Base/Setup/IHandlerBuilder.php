<?php
namespace Eddy\Base\Setup;


use Eddy\Object\HandlerObject;


/**
 * @skeleton
 */
interface IHandlerBuilder
{
	public function buildHandler($item): ?HandlerObject;
}