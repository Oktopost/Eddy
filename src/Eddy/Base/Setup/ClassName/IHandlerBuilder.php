<?php
namespace Eddy\Base\Setup\ClassName;


use Eddy\Objects\HandlerObject;


/**
 * @skeleton
 */
interface IHandlerBuilder
{
	public function tryBuild(string $item): ?HandlerObject;
}