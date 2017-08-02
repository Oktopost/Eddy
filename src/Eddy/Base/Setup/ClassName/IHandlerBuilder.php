<?php
namespace Eddy\Base\Setup\ClassName;


use Eddy\Object\HandlerObject;


/**
 * @skeleton
 */
interface IHandlerBuilder
{
	public function tryBuild(string $item): ?HandlerObject;
}