<?php
namespace Eddy\Base\Config;


use Eddy\Base\Engine\ILockProvider;
use Eddy\Base\Engine\IQueueProvider;


/**
 * @property IQueueProvider $QueueProvider
 * @property ILockProvider $Locker
 */
interface IEngineConfig
{
	
}