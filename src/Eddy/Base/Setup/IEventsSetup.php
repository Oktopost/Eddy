<?php
namespace Eddy\Base\Setup;


use Eddy\Objects\EventObject;
use Eddy\Objects\HandlerObject;


/**
 * @property HandlerObject[]	$Handlers
 * @property EventObject[]		$Events
 * @property array[]			$Subscribers
 */
interface IEventsSetup
{
	public function addSubscriber(string $event, string $handler): void;
}