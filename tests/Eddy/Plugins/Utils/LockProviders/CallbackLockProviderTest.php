<?php
namespace Eddy\Plugins\Utils\LockProviders;


use Eddy\Base\Engine\Lock\ILocker;

use PHPUnit\Framework\TestCase;


class CallbackLockProviderTest extends TestCase
{
	private function getSubject(callable $arg): CallbackLockProvider
	{
		return new CallbackLockProvider($arg);
	}
	
	
	public function test_get()
	{
		$callback = function (string $queueName) { return new DummyTestCallbackLockProvider_Locker($queueName); };
		
		/** @var DummyTestClassNameLockProvider_Locker|ILocker $locker */
		$locker = $this->getSubject($callback)->get('test');
		
		self::assertInstanceOf(ILocker::class, $locker);
		self::assertEquals('test', $locker->queueName);
	}
}

class DummyTestCallbackLockProvider_Locker implements ILocker
{
	public $queueName;
	
	
	public function __construct(string $queueName)
	{
		$this->queueName = $queueName;	
	}


	public function lock(float $timeoutSeconds = -1.0): bool { return true; }

	public function isLocked(): bool { return false; }

	public function unlock(): bool { return true; }
}