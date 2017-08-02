<?php
namespace Eddy\Base\Setup;


use Eddy\Object\EventObject;
use Eddy\Object\HandlerObject;


/**
 * @skeleton
 */
interface ISetupBuilder
{
	/**
	 * @param string|array|EventObject|HandlerObject $item
	 */
	public function add($item): void;
	
	public function get(): IEventsSetup;
}