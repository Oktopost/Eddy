<?php
namespace Eddy\Base\Setup\ClassName;


use Eddy\Objects\EventObject;


/**
 * @skeleton
 */
interface IEventBuilder
{
	public function tryBuild(string $item): ?EventObject;
}