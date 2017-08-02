<?php
namespace Eddy\Base\Setup;


use Eddy\Object\EventObject;


/**
 * @skeleton
 */
interface IEventBuilder
{
	public function tryLoad($item): ?EventObject;
}