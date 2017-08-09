<?php
namespace Eddy\Engine\Queue\DeepQueue;


use Eddy\Base\Engine\Queue\IQueueObjectManager;

use DeepQueue\DeepQueue;
use DeepQueue\Base\IQueueObject;


class DeepQueueObjectManagerAdapter implements IQueueObjectManager
{
	private $deepQueue;
	
	
	public function __construct(DeepQueue $deepQueue)
	{
		$this->deepQueue = $deepQueue;
	}
	
	
	public function load(string $name): ?IQueueObject
	{
		return $this->deepQueue->config()->manager()->load($name);
	}
	
	public function save(IQueueObject $object): void
	{
		if (!$object->Id)
		{
			$this->deepQueue->config()->manager()->create($object);
		}
		else
		{
			$this->deepQueue->config()->manager()->update($object);
		}
	}
}