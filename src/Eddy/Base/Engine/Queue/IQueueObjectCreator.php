<?php
namespace Eddy\Base\Engine\Queue;


use Eddy\Base\IEddyQueueObject;


/**
 * @skeleton
 */
interface IQueueObjectCreator
{
	public function createQueue(IEddyQueueObject $eddyObject): void;

	/**
	 * @param IEddyQueueObject[]|array $eddyObjects
	 */
	public function createQueues(array $eddyObjects): void;
}