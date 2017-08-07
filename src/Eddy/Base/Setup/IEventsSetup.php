<?php
namespace Eddy\Base\Setup;


use Eddy\Object\EventObject;
use Eddy\Object\HandlerObject;


/**
 * @property HandlerObject[]	$Handlers
 * @property EventObject[]		$Events
 * @property array[]			$Subscribers
 */
interface IEventsSetup
{
	public function addSubscriber(string $event, string $handler): void;
}