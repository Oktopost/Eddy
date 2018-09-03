<?php
namespace Eddy\Tests;


use Eddy\Scope;
use Eddy\IEvents;
use Eddy\IEventProxy;


class TestEventProvider implements IEvents
{
	/** @var TestQueue */
	private $testQueue;
	
	
	public function __construct(TestQueue $queue)
	{
		$this->testQueue = $queue;
	}


	public function getTestQueue(): TestQueue
	{
		return $this->testQueue;
	}
	
	/**
	 * @param string $className
	 * @return mixed
	 */
	public function event(string $className)
	{
		/** @var IEventProxy $obj */
		$obj = Scope::skeleton()->load(new $className);
		$obj->setPublisher($this->testQueue->getPublisher($className));
		
		return $obj;
	}
}