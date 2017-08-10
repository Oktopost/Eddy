<?php
namespace Eddy\Base\Module;


use Eddy\Base\IEddyQueueObject;


/**
 * @skeleton
 */
interface IEddyObjectModule
{
	public function getByQueueName(string $queueName): ?IEddyQueueObject;

	/**
	 * @return IEddyQueueObject[]|array
	 */
	public function getAllRunning(): array;
}