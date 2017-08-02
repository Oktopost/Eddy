<?php
namespace Eddy\Base\Setup;


use Eddy\Object\HandlerObject;


interface IHandlerLoader
{
	public function tryLoad($item): ?HandlerObject;
}