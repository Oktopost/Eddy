<?php
namespace Eddy\Base;


use Eddy\Base\Config\IConfigConsumer;
use Eddy\Object\EventObject;


interface IEngine extends IConfigConsumer
{
	public function config(): IConfig;

	/**
	 * @param EventObject $object
	 * @return mixed
	 */
	public function event(EventObject $object);
}