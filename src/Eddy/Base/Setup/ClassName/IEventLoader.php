<?php
namespace Eddy\Base\Setup\ClassName;


use Eddy\Object\EventObject;


interface IEventLoader
{
	public function tryLoad($item): ?EventObject;
}