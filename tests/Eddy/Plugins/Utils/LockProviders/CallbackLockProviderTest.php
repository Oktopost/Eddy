<?php
namespace Eddy\Plugins\Utils\LockProviders;


use Eddy\Base\Engine\Lock\ILocker;

use Eddy\Base\IExceptionHandler;
use PHPUnit\Framework\TestCase;


class CallbackLockProviderTest extends TestCase
{
	private function getSubject(callable $arg): CallbackLockProvider
	{
		$provider = new CallbackLockProvider($arg);
		$provider->setTTL(333);
		
		$handler = new class implements IExceptionHandler
		{
			public function exception(\Throwable $t): void {}
		};
		
		$provider->setErrorHandler($handler);
		
		return $provider;
	}
	
	
	public function test_get_anonymousFunction()
	{
		$callback = function (string $queueName) { return new DummyTestCallbackLockProvider_Locker($queueName); };
		
		/** @var DummyTestClassNameLockProvider_Locker|ILocker $locker */
		$locker = $this->getSubject($callback)->get('test');
		
		self::assertInstanceOf(ILocker::class, $locker);
		self::assertEquals('test', $locker->queueName);
	}
	
	public function test_get_classMethod()
	{
		$callback = [new CallbackClass_LockProviderTest(), 'get'];
		
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

	public function setErrorHandler(IExceptionHandler $handler): void
	{
		// TODO: Implement setErrorHandler() method.
	}

	public function lock(): bool { return true; }

	public function isLocked(): bool { return false; }

	public function unlock(): bool { return true; }
}

class CallbackClass_LockProviderTest 
{
	function get(string $queueName)
	{
		return new DummyTestCallbackLockProvider_Locker($queueName);
	}
}