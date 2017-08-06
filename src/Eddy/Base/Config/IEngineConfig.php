<?php
namespace Eddy\Base\Config;


use Eddy\Base\Engine\ILockProvider;
use Eddy\Base\Engine\Processor\IProcessController;
use Eddy\Base\Engine\Queue\IQueueProvider;
use Eddy\Base\Engine\Queue\IQueueDecorator;


/**
 * @property IQueueProvider			$QueueProvider
 * @property ILockProvider			$Locker
 * @property IQueueDecorator[]		$QueueDecorators
 * @property IProcessController[]	$Controllers
 */
interface IEngineConfig
{
	public function addDecorator(...$decorators): void;
	public function addController(...$controllers): void;
	
	public function setQueueProvider($config): IEngineConfig;
}