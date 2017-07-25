<?php
namespace Eddy;


use Eddy\Base\IConfig;
use Eddy\Base\IEngine;
use Eddy\Base\Engine\Publish\IPublisher;
use Eddy\Engine\Proxy\DefaultProxy;
use Eddy\Exceptions\InvalidEventException;

use Eddy\Object\EventObject;


class Engine implements IEngine
{
	private $config;
	
	
	private function getEventObject(string $name): EventObject
	{
		$object = $this->config()->DAL()->events()->loadByIdentifier($name);
		
		if (!$object)
			throw new InvalidEventException($name);
		
		return $object;
	}
	
	private function getProxy(EventObject $object): IEventProxy
	{
		return ($object->ProxyClassName ? 
			new $object->ProxyClassName : 
			new DefaultProxy($object->EventInterface));
	}
	
	/**
	 * @param EventObject $object
	 * @return mixed
	 */
	private function getPublisher(EventObject $object): IPublisher
	{
		
	}
	
	
	public function setConfig(IConfig $config): void
	{
		$this->config = $config;
	}

	public function config(): IConfig
	{
		return $this->config;
	}
	

	/**
	 * @param $name
	 * @return mixed
	 */
	public function event(string $name)
	{
		$event = $this->getEventObject($name);
		$proxy = $this->getProxy($event);
		$publisher = $this->getPublisher($event);
		
		$proxy->setPublisher($publisher);
		
		return $proxy;
	}
}