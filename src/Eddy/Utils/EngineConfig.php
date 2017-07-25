<?php
namespace Eddy\Utils;


use Eddy\Scope;
use Eddy\Base\Config\IEngineConfig;
use Eddy\Base\Engine\ILockProvider;
use Eddy\Base\Engine\Queue\IQueueDecorator;
use Eddy\Base\Engine\Queue\IQueueProvider;
use Eddy\Engine\Queue\DeepQueue\DeepQueueProvider;
use Eddy\Exceptions\UnexpectedException;

use Objection\LiteSetup;
use Objection\LiteObject;

use DeepQueue\DeepQueue;


/**
 * @property IQueueProvider	$QueueProvider
 * @property ILockProvider	$Locker
 * @property []				$QueueDecorators
 */
class EngineConfig extends LiteObject implements IEngineConfig
{
	/** @var IQueueDecorator[] */
	private $queueDecorators;
	
	
	/**
	 * @return array
	 */
	protected function _setup()
	{
		return [
			'QueueProvider' 	=> LiteSetup::createInstanceOf(IQueueProvider::class),
			'Locker'			=> LiteSetup::createInstanceOf(ILockProvider::class),
			'QueueDecorators'	=> LiteSetup::createArray()
		];
	}
	
	
	/**
	 * @param IQueueProvider|DeepQueue $config
	 * @return EngineConfig
	 */
	public function setQueueProvider($config): EngineConfig
	{
		if ($config instanceof DeepQueue) $this->QueueProvider = new DeepQueueProvider($config);
		else if ($config instanceof IQueueProvider) $this->QueueProvider = $config;
		else if (is_string($config)) return $this->setQueueProvider(Scope::skeleton($config));
		else
		{
			throw new UnexpectedException('Invalid input. $config must be IQueueProvider, ' . 
				'DeepQueue instance, or interface name');
		}
		
		return $this;
	}
	
	public function addDecorator(...$decorators)
	{
		if (is_array($decorators[0]))
			$decorators = $decorators[0];
		
		$this->QueueDecorators = array_merge($this->QueueDecorators, $decorators);
	}
}