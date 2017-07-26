<?php
namespace Eddy;


use Eddy\Base\IConfig;
use Eddy\Base\IEngine;
use Eddy\Base\Engine\Publish\IPublisher;
use Eddy\Base\Engine\Publish\IPublisherObject;
use Eddy\Engine\Proxy\DefaultProxy;

use Eddy\Object\EventObject;


class Engine implements IEngine
{
	private $config;
	
	
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
		/** @var IPublisherObject $publisher */
		$publisher = Scope::skeleton(IPublisherObject::class);
		$publisher->setConfig($this->config());
		$publisher->setObject($object);
		
		return $publisher;
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
	 * @param EventObject $object
	 * @return mixed
	 */
	public function event(EventObject $object)
	{
		$proxy = $this->getProxy($object);
		$publisher = $this->getPublisher($object);
		
		$proxy->setPublisher($publisher);
		
		return $proxy;
	}
}