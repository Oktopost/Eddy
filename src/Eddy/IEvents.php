<?php
namespace Eddy;


interface IEvents
{
	/**
	 * @param string $className
	 * @return mixed
	 */
	public function event(string $className);
}