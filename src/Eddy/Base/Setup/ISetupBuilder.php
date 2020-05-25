<?php
namespace Eddy\Base\Setup;


use Eddy\Objects\EventObject;
use Eddy\Objects\HandlerObject;


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