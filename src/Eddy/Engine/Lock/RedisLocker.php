<?php
namespace Eddy\Engine\Lock;


use Eddy\Base\IExceptionHandler;
use Eddy\Base\Engine\Lock\ILocker;

use Predis\Client;


class RedisLocker implements ILocker
{
	private const LOCK_KEY = 'Locks';
	
	
	private $queueName;
	
	/** @var Client */
	private $client;
	
	private $ttl;
	
	/** @var IExceptionHandler */
	private $errorHandler;
	
	
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

	
	public function setErrorHandler(IExceptionHandler $handler): void
	{
		$this->errorHandler = $handler;
	}

	public function lock(float $timeoutSeconds = -1.0): bool
	{
		try
		{
			$result = $this->client->set($this->getKey(), time(), 'EX', $this->ttl, 'NX');
		}
		catch (\Throwable $e)
		{
			$this->errorHandler->exception($e);
			return false;
		}
		
		return (bool)$result;
	}

	public function isLocked(): bool
	{
		try
		{
			return (bool)$this->client->exists($this->getKey());
		}
		catch (\Throwable $e)
		{
			$this->errorHandler->exception($e);
			return true;
		}
	}

	public function unlock(): bool
	{
		try
		{
			return (bool)$this->client->del([$this->getKey()]);
		}
		catch (\Throwable $e)
		{
			$this->errorHandler->exception($e);
			return false;
		}
	}
}