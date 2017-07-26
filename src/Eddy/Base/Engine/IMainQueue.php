<?php
namespace Eddy\Base\Engine;


use Eddy\Base\IEddyQueueObject;
use Eddy\Base\Config\IConfigConsumer;


/**
 * @skeleton
 */
interface IMainQueue extends IConfigConsumer
{
	public function schedule(IEddyQueueObject $object): void;
}