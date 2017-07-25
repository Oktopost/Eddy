<?php
namespace Eddy\Utils;


use Eddy\Base\Config\IEngineConfig;
use Eddy\Base\Engine\ILockProvider;
use Eddy\Base\Engine\IQueueProvider;
use Eddy\Exceptions\UnexpectedException;

use Objection\LiteSetup;
use Objection\LiteObject;

use DeepQueue\DeepQueue;


/**
 * @property IQueueProvider $QueueProvider
 * @property ILockProvider $Locker
 */
class EngineConfig extends LiteObject implements IEngineConfig
{
	/**
	 * @return array
	 */
	protected function _setup()
	{
		return [
			'QueueProvider' => LiteSetup::createInstanceOf(IQueueProvider::class),
			'Locker'		=> LiteSetup::createInstanceOf(ILockProvider::class)
		];
	}


	/**
	 * @param IQueueProvider|DeepQueue $config
	 * @return EngineConfig
	 */
	public function setQueueProvider($config): EngineConfig
	{
		if ($config instanceof DeepQueue) $this->QueueProvider = $config;
		else if ($config instanceof IQueueProvider) $this->QueueProvider = $config;
		else if (is_string($config)) $this->QueueProvider = new $config;
		else
		{
			throw new UnexpectedException('Invalid input. $config must be IQueueProvider, ' . 
				'DeepQueue instance, or class name');
		}
		
		return $this;
	}
}