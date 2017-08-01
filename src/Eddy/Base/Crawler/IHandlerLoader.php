<?php
namespace Eddy\Base\Crawler;


use Eddy\Object\HandlerObject;


interface IHandlerLoader
{
	public function tryLoad($item): HandlerObject;
}