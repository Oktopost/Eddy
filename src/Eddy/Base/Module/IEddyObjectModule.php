<?php
namespace Eddy\Base\Module;


use Eddy\Base\IEddyQueueObject;


/**
 * @skeleton
 */
interface IEddyObjectModule
{
	public function getByQueueName(string $queueName): ?IEddyQueueObject;
}