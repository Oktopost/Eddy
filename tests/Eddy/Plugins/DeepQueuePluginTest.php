<?php
namespace Eddy\Plugins;


use DeepQueue\DeepQueue;
use DeepQueue\PreparedConfiguration\PreparedQueue;
use Eddy\Engine\Queue\DeepQueue\DeepQueueProvider;
use Eddy\Utils\Config;
use PHPUnit\Framework\TestCase;


class DeepQueuePluginTest extends TestCase
{
	private function getSubject(): DeepQueuePlugin
	{
		return new DeepQueuePlugin(PreparedQueue::InMemory());
	}
	
	
	public function test_setup()
	{
		$config = new Config();
		
		$this->getSubject()->setup($config);
		
		self::assertInstanceOf(DeepQueueProvider::class, $config->Engine->QueueProvider);
	}
}