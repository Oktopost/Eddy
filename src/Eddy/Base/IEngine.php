<?php
namespace Eddy\Base;


use Eddy\Object\EventObject;


interface IEngine
{
	/**
	 * @param EventObject $object
	 * @return mixed
	 */
	public function event(EventObject $object);
}