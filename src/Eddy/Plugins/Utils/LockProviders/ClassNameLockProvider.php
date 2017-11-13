<?php
namespace Eddy\Plugins\Utils\LockProviders;


use Eddy\Base\Engine\ILockProvider;
use Eddy\Base\Engine\Lock\ILocker;
use Eddy\Exceptions\InvalidUsageException;


class ClassNameLockProvider implements ILockProvider
{
	private $className;
	
	
	private function validateClass(string $lockerClassName): void
	{
		$interfaces = class_implements($lockerClassName);
		
		if (!in_array(ILocker::class, $interfaces))
		{
			throw new InvalidUsageException('Locker class must implement ILocker interface');
		}
	}
	
	
	public function __construct(string $lockerClassName)
	{
		$this->validateClass($lockerClassName);
		
		$this->className = $lockerClassName;
	}


	public function get($queueName): ILocker
	{
		return new $this->className($queueName);
	}

	public function setTTL(int $ttl): void
	{
		return;
	}
}