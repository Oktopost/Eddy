<?php
namespace Eddy\Tests;


use Eddy\Base\Engine\Publish\IPublisher;
use Eddy\Tests\Base\ITestQueue;


class TestPublisher implements IPublisher
{
	/** @var string */
	private $name;
	
	/** @var ITestQueue */
	private $queue;
	
	
	public function __construct(ITestQueue $queue, string $name)
	{
		$this->name = $name;
		$this->queue = $queue;
	}


	public function publish(array $data): void
	{
		$this->queue->publish($this->name, $data);
	}
}