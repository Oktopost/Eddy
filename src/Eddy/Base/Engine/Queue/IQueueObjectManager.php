<?php
namespace Eddy\Base\Engine\Queue;


use DeepQueue\Base\IQueueObject;


interface IQueueObjectManager
{
	public function load(string $name): ?IQueueObject;
	public function save(IQueueObject $object): void;
}