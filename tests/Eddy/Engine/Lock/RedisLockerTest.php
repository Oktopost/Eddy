<?php
namespace Eddy\Engine\Lock;


use PHPUnit\Framework\TestCase;
use Predis\Client;


class RedisLockerTest extends TestCase
{
	private const QUEUE_NAME = 'testQueue';
	
	
	/** @var Client */
	private $client;
	
	
	private function getSubject(): RedisLocker
	{
		return new RedisLocker(self::QUEUE_NAME, $this->client);
	}
	
	
	protected function setUp()
	{
		$redisCfg = [
			'scheme'	=> 'tcp',
			'host'		=> '127.0.0.1',
			'port'		=> '6379',
			'ssl'		=> [],
			'prefix'	=> 'stats-test:'	
		];
		
		$this->client = new Client($redisCfg, ['prefix' => 'lock-test:']);
		
		$this->client->eval("return redis.call('del', 'defaultKey', unpack(redis.call('keys', ARGV[1])))", 
			0, 'lock-test:*');
	}
	
	
	public function test_lock_NotLocked_ReturnTrue()
	{
		self::assertTrue($this->getSubject()->lock());
	}
	
	public function test_lock_AlreadyLocked_ReturnFalse()
	{
		$this->getSubject()->lock();
		
		self::assertFalse($this->getSubject()->lock());
	}
	
	public function test_isLocked_LockNotExist_ReturnFalse()
	{
		self::assertFalse($this->getSubject()->isLocked());
	}
	
	public function test_isLocked_LockExist_ReturnTrue()
	{
		$this->getSubject()->lock();
		
		self::assertTrue($this->getSubject()->isLocked());
	}
	
	public function test_unlock_NoLockExist_ReturnFalse()
	{
		self::assertFalse($this->getSubject()->unlock());
	}
	
	public function test_unlock_LockExist_ReturnTrueNoMoreLock()
	{
		$this->getSubject()->lock();
		
		self::assertTrue($this->getSubject()->unlock());
		self::assertFalse($this->getSubject()->isLocked());
	}
}