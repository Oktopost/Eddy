<?php
namespace Eddy\Plugins;


use Eddy\Plugins\Utils\LockProviders\RedisLockProvider;
use Eddy\Utils\Config;
use PHPUnit\Framework\TestCase;


class RedisLockerPluginTest extends TestCase
{
	private function getSubject(): RedisLockerPlugin
	{
		return new RedisLockerPlugin([]);
	}
	
	
	public function test_setup()
	{
		$config = new Config();
		
		$this->getSubject()->setup($config);
		
		self::assertInstanceOf(RedisLockProvider::class, $config->Engine->Locker);
	}
}