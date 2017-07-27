<?php
namespace Eddy;


use Eddy\Base\IConfig;
use Eddy\Base\IEngine;
use Eddy\Base\Engine\Publish\IPublisher;
use Eddy\Base\Engine\Publish\IPublishBuilder;
use Eddy\Engine\Proxy\DefaultProxy;

use Eddy\Object\EventObject;


/**
 * @autoload
 */
class Engine implements IEngine
{
	/** @context */
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
		/** @var IPublishBuilder $publisher */
		$publisher = Scope::skeleton(IPublishBuilder::class);
		$publisher->setConfig($this->config);
		
		return $publisher->getEventPublisher($object);
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