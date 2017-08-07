<?php
namespace Eddy;


use DeepQueue\PreparedConfiguration\PreparedQueue;
use Eddy\Plugins\DeepQueuePlugin;
use Eddy\Plugins\ExecutorLoggerPlugin;
use Eddy\Plugins\RedisLockerPlugin;
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

		$eddy->addPlugin([
				new DeepQueuePlugin(PreparedQueue::Redis($this->getRedisConfig())),
				new RedisLockerPlugin($this->getRedisConfig()),
				new StatisticsCollectorPlugin(MySQLConfig::connector(), $this->getRedisConfig(), 120),
				new ExecutorLoggerPlugin()]);
		
		$eddy->config()->Setup->addCrawlerSetup(__DIR__ . '/TestEvents', 'SanityTestNS');
		
	//	$eddy->runSetup();
	}
}