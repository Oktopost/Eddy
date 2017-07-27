<?php
namespace Eddy\Base;


use Eddy\Object\EventObject;


interface IEngine
{
	public function config(): IConfig;

	/**
	 * @param EventObject $object
	 * @return mixed
	 */
	public function event(EventObject $object);
}