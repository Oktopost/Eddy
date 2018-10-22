<?php
namespace Eddy\Plugins;


use Eddy\IEddyPlugin;
use Eddy\Plugins\Utils\LockProviders\RedisLockProvider;
use Eddy\Utils\Config;


class RedisLockerPlugin implements IEddyPlugin
{
	/** @var RedisLockProvider */
	private $lockProvider;
	
	
	public function __construct(array $redisConfig)
	{
		$this->lockProvider = new RedisLockProvider($redisConfig);
	}


	public function setup(Config $config)
	{
		$this->lockProvider->setTTL($config->Engine->LockTTLSec);
		$this->lockProvider->setErrorHandler($config->ExceptionHandler);
		
		$config->Engine->Locker = $this->lockProvider;
	}
}