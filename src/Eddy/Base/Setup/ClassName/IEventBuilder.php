<?php
namespace Eddy\Base\Setup\ClassName;


use Eddy\Object\EventObject;


/**
 * @skeleton
 */
interface IEventBuilder
{
	public function tryBuild(string $item): ?EventObject;
}