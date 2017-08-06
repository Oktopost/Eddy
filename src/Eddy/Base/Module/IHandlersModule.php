<?php
namespace Eddy\Base\Module;


use Eddy\Object\HandlerObject;


interface IHandlersModule
{
	public function pause(HandlerObject $object): void;
}