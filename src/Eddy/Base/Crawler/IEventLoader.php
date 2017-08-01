<?php
namespace Eddy\Base\Crawler;


use Eddy\Object\EventObject;


interface IEventLoader
{
	public function tryLoad($item): ?EventObject;
}