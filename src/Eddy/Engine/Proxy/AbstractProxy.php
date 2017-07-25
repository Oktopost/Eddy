<?php
namespace Eddy\Engine\Proxy;


use Eddy\IEventProxy;
use Eddy\Base\Engine\Publish\IPublisher;


class AbstractProxy implements IEventProxy
{
	/** @var IPublisher */
	private $publisher;
	
	
	protected function publisher(): IPublisher
	{
		return $this->publisher;
	}
	
	protected function publish(array $data)
	{
		$this->publisher->publish($data);
	}
	
	
	public function setPublisher(IPublisher $publisher): void
	{
		$this->publisher = $publisher;
	}
}