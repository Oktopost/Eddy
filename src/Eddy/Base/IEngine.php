<?php
namespace Eddy\Base;


use Eddy\Objects\EventObject;


interface IEngine
{
	/**
	 * @param EventObject $object
	 * @return mixed
	 */
	public function event(EventObject $object);
}