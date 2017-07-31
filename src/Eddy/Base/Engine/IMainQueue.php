<?php
namespace Eddy\Base\Engine;


use Eddy\Base\IEddyQueueObject;
use Eddy\Base\Config\IConfigConsumer;


/**
 * @skeleton
 */
interface IMainQueue
{
	public function schedule(IEddyQueueObject $object): void;
}