<?php
namespace Eddy\Engine\Queue\DeepQueue;


use Eddy\Base\Engine\IQueue;
use Eddy\Base\Engine\Queue\IQueueManager;
use Eddy\Base\Engine\Queue\IQueueProvider;

use DeepQueue\Enums\QueueLoaderPolicy;
use DeepQueue\PreparedConfiguration\PreparedQueue;

use PHPUnit\Framework\TestCase;


class DeepQueueProviderTest extends TestCase
{
	private function getSubject(): IQueueProvider
	{
		$dq = PreparedQueue::InMemory();
		$dq->config()
			->setQueueNotExistsPolicy(QueueLoaderPolicy::CREATE_NEW);
		
		return new DeepQueueProvider($dq);
	}
	
	
	public function test_getQueue_IQueueInstanceReturned()
	{
		self::assertInstanceOf(IQueue::class, $this->getSubject()->getQueue('test'));
	}
	
	public function test_getManager_IQueueManagerInstanceReturned()
	{
		self::assertInstanceOf(IQueueManager::class, $this->getSubject()->getManager('test'));
	}
}