<?php
namespace Eddy\Utils;


use Eddy\Engine\Queue\DeepQueue\DeepQueueProvider;

use DeepQueue\DeepQueue;
use PHPUnit\Framework\TestCase;


class EngineConfigTest extends TestCase
{
	public function test_addDecorator_PassSingleInstance()
	{
		$config = new EngineConfig();
		$config->addDecorator('a');
		
		self::assertEquals(['a'], $config->QueueDecorators);
	}
	
	public function test_addDecorator_QueueNotEmpty()
	{
		$config = new EngineConfig();
		
		$config->addDecorator('a');
		$config->addDecorator('b');
		
		self::assertEquals(['a', 'b'], $config->QueueDecorators);
	}
	
	public function test_addDecorator_AddArray()
	{
		$config = new EngineConfig();
		
		$config->addDecorator('a');
		$config->addDecorator(['b', 'c']);
		
		self::assertEquals(['a', 'b', 'c'], $config->QueueDecorators);
	}
	
	
	public function test_setQueueProvider_PassDeepQueueInstance()
	{
		$config = new EngineConfig();
		$config->setQueueProvider(new DeepQueue());
		
		self::assertInstanceOf(DeepQueueProvider::class, $config->QueueProvider);
	}
	
	public function test_setQueueProvider_PassQueueProvider()
	{
		$config = new EngineConfig();
		
		/** @var DeepQueueProvider $provider */
		$provider = $this->getMockBuilder(DeepQueueProvider::class)->disableOriginalConstructor()->getMock();
		
		$config->setQueueProvider($provider);
		
		self::assertEquals($provider, $config->QueueProvider);
	}
	
	public function test_setQueueProvider_PassClassName()
	{
		$config = new EngineConfig();
		
		$provider = $this->getMockBuilder(DeepQueueProvider::class)->disableOriginalConstructor()->getMock();
		\UnitTestScope::override('abc', $provider);
		
		$config->setQueueProvider('abc');
		
		self::assertEquals($provider, $config->QueueProvider);
		
		
		\UnitTestScope::clear();
	}

	/**
	 * @expectedException \Eddy\Exceptions\UnexpectedException
	 */
	public function test_setQueueProvider_InvalidItemPassed_ExceptionThrown()
	{
		$config = new EngineConfig();
		$config->setQueueProvider(123);
	}
}