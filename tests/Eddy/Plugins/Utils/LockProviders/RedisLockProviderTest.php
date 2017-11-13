<?php
namespace Eddy\Plugins\Utils\LockProviders;


use Eddy\Base\Engine\Lock\ILocker;
use PHPUnit\Framework\TestCase;


class RedisLockProviderTest extends TestCase
{
	private function getSubject(): RedisLockProvider
	{
		$provider = new RedisLockProvider([
			'scheme'	=> 'tcp',
			'host'		=> '127.0.0.1',
			'port'		=> '6379',
			'prefix'	=> 'locker-test:'	
		]);
		
		$provider->setTTL(300);
		
		return $provider;
	}

	
	/**
	 * @expectedException \Eddy\Exceptions\InvalidUsageException
	 */
	public function test_get_notStringArgument()
	{
		$this->getSubject()->get([]);
	}
	
	public function test_get_returnILocker()
	{
		self::assertInstanceOf(ILocker::class, $this->getSubject()->get('test'));
	}
}