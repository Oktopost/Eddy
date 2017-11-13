<?php
namespace Eddy\Engine\Lock;


use Eddy\Base\Engine\Lock\ILocker;

use Predis\Client;


class RedisLocker implements ILocker
{
	private const LOCK_KEY = 'Locks';
	
	
	private $queueName;
	
	/** @var Client */
	private $client;
	
	private $ttl;
	
	
	private function getKey(): string 
	{
		return self::LOCK_KEY . $this->queueName;
	}
	
	
	public function __construct(string $queueName, Client $client, int $ttl)
	{
		$this->queueName = $queueName;
		$this->client = $client;
		
		$this->ttl = $ttl;
	}


	public function lock(float $timeoutSeconds = -1.0): bool
	{
		$result = $this->client->set($this->getKey(), time(), 'EX', $this->ttl, 'NX');
		return (bool)$result;
	}

	public function isLocked(): bool
	{
		return (bool)$this->client->exists($this->getKey());
	}

	public function unlock(): bool
	{
		return (bool)$this->client->del([$this->getKey()]);
	}
}