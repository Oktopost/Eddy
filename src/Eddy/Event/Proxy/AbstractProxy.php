<?php

namespace Eddy\Event\Proxy;


use Eddy\Engine\Base\Proxy\IProxy;
use Eddy\Engine\Base\Publisher\IPublisher;

class AbstractProxy implements IProxy
{
	/**
	 * @param mixed $payload
	 */
	public function publish(array $payload): void
	{
		// TODO: Implement publish() method.
	}
	
	
	protected function publish($data)
	{
		
	}
}