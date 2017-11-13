<?php
namespace Eddy\Plugins\Utils\LockProviders;


use Eddy\Base\Engine\ILockProvider;
use Eddy\Base\Engine\Lock\ILocker;
use Eddy\Engine\Lock\RedisLocker;
use Eddy\Exceptions\InvalidUsageException;

use Predis\Client;


class RedisLockProvider implements ILockProvider
{
	/** @var Client */
	private $client;
	
	private $ttl;
	
	
	private function initClient(array $redisConfig): void
	{
		$options = [];
		
		if (isset($redisConfig['prefix']))
		{
			$options['prefix'] = $redisConfig['prefix'];
		}
		
		$this->client = new Client($redisConfig, $options);
	}
	
	
	public function __construct(array $redisConfig)
	{
		$this->initClient($redisConfig);
	}


	public function get($queueName): ILocker
	{
		if (!is_string($queueName))
		{
			throw new InvalidUsageException('Redis lock provider expects queue name as a string');
		}
		
		return new RedisLocker($queueName, $this->client, $this->ttl);
	}

	public function setTTL(int $ttl): void
	{
		$this->ttl = $ttl;
	}
}