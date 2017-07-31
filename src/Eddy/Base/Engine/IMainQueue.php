<?php
namespace Eddy\Base\Engine;


use Eddy\Base\IEddyQueueObject;


/**
 * @skeleton
 */
interface IMainQueue
{
	public function schedule(IEddyQueueObject $object): void;
}