<?php
namespace Eddy\Plugins\Utils\LockProviders;


use Eddy\Base\Engine\ILockProvider;
use Eddy\Base\Engine\Lock\ILocker;
use Eddy\Base\IExceptionHandler;
use Eddy\Exceptions\InvalidUsageException;


class ClassNameLockProvider implements ILockProvider
{
	private $className;
	
	/** @var IExceptionHandler */
	private $errorHandler;
	
	
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

	
	public function setErrorHandler(IExceptionHandler $handler): void
	{
		$this->errorHandler = $handler;
	}

	public function get($queueName): ILocker
	{
		/** @var ILocker $locker */
		$locker = new $this->className($queueName);
		$locker->setErrorHandler($this->errorHandler);
		
		return $locker;
	}

	public function setTTL(int $ttl): void
	{
		return;
	}
}