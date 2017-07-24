<?php
namespace Eddy\Base\Module;


use Eddy\Object\EventObject;
use Eddy\Object\HandlerObject;


interface IEventHandlerModule
{
	public function subscribe(EventObject $event, HandlerObject $handler): void;
	public function unsubscribe(EventObject $event, HandlerObject $handler): void;
}