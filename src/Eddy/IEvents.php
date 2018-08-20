<?php
namespace Eddy;


interface IEvents
{
	/**
	 * @param string $interface
	 * @return mixed
	 */
	public function event(string $interface);
}