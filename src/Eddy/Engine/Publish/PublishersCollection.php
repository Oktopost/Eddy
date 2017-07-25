<?php
namespace Eddy\Engine\Publish;


use Eddy\Base\Engine\Publish\IPublisher;


class PublishersCollection implements IPublisher
{
	/** @var IPublisher[] */
	private $collection = [];

	
	public function add(...$publishers)
	{
		if (is_array($publishers[0]))
			$publishers = $publishers[0];
		
		$this->collection = array_merge($this->collection, $publishers);
	}
	
	
	public function publish(array $data): void
	{
		foreach ($this->collection as $publisher)
		{
			$publisher->publish($data);
		}
	}
}