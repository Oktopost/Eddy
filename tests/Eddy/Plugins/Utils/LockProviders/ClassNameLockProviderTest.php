<?php
namespace Eddy\Plugins\Utils\LockProviders;


use Eddy\Base\Engine\Lock\ILocker;

use PHPUnit\Framework\TestCase;


class ClassNameLockProviderTest extends TestCase
{
	private function getSubject(string $className): ClassNameLockProvider
	{
		return new ClassNameLockProvider($className);
	}
	
	
	/**
	 * @expectedException \Eddy\Exceptions\InvalidUsageException
	 */
	public function test_notILockerClassProvided()
	{
		$this->getSubject(self::class);
	}
	
	public function test_ILockerClassProvided_InstanceOfILockerReturned()
	{
		/** @var DummyTestClassNameLockProvider_Locker|ILocker $locker */
		$locker = $this->getSubject(DummyTestClassNameLockProvider_Locker::class)->get('test');
		
		self::assertInstanceOf(ILocker::class, $locker);
		self::assertEquals('test', $locker->queueName);
	}
}


class DummyTestClassNameLockProvider_Locker implements ILocker
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