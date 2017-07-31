<?php
namespace Eddy;


use Eddy\Base\IEngine;
use Eddy\Engine\Proxy\DefaultProxy;

use Eddy\Object\EventObject;


/**
 * @autoload
 */
class Engine implements IEngine
{
	/**
	 * @autoload
	 * @var \Eddy\Base\Engine\Publish\IPublishBuilder
	 */
	private $publishBuilder;
	
	
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
	public function event(EventObject $object)
	{
		$proxy = $this->getProxy($object);
		$publisher = $this->publishBuilder->getEventPublisher($object);
		
		$proxy->setPublisher($publisher);
		
		return $proxy;
	}
}