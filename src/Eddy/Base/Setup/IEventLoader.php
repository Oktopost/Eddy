<?php
namespace Eddy\Base\Setup;


use Eddy\Object\EventObject;


interface IEventLoader
{
	public function tryLoad($item): ?EventObject;
}