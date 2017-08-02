<?php
namespace Eddy\Base\Setup\ClassName;


use Eddy\Object\HandlerObject;


interface IHandlerLoader
{
	public function tryLoad($item): ?HandlerObject;
}