<?php
namespace Eddy\Base\Config;


use Eddy\Base\Engine\ILockProvider;
use Eddy\Base\Engine\Queue\IQueueProvider;
use Eddy\Base\Engine\Queue\IQueueDecorator;


/**
 * @property IQueueProvider $QueueProvider
 * @property ILockProvider $Locker
 * @property IQueueDecorator[] $QueueDecorators
 */
interface IEngineConfig
{
	
}