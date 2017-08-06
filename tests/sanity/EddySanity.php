<?php
namespace Eddy;


use DeepQueue\PreparedConfiguration\PreparedQueue;
use Eddy\Plugins\ExecutorLoggerPlugin;
use Eddy\Plugins\StatisticsCollectorPlugin;
use Eddy\Plugins\Utils\LockProviders\RedisLockProvider;
use lib\MySQLConfig;
use PHPUnit\Framework\TestCase;


class EddySanity extends TestCase
{
	private function getRedisConfig(): array 
	{
		return [
			'scheme'	=> 'tcp',
			'host'		=> '127.0.0.1',
			'port'		=> '6379',
			'prefix'	=> 'sanity-test:'	
		];
	}
	
	public function test_sanity()
	{
		$eddy = new Eddy();

		$eddy->config()->setMainDataBase(MySQLConfig::connector());
		$eddy->config()->Engine->setQueueProvider(PreparedQueue::Redis($this->getRedisConfig()));
		$eddy->config()->Engine->Locker = new RedisLockProvider($this->getRedisConfig());
		
		$eddy->addPlugin([
				new StatisticsCollectorPlugin(MySQLConfig::connector(), $this->getRedisConfig(), 120),
				new ExecutorLoggerPlugin()]);
	}
}