<?php
namespace Eddy\Plugins\Utils\LockProviders;


use Eddy\Base\Engine\ILockProvider;
use Eddy\Base\Engine\Lock\ILocker;
use Eddy\Base\IExceptionHandler;


class CallbackLockProvider implements ILockProvider
{
	private $callback;
	
	/** @var IExceptionHandler */
	private $errorHandler;
	
	
	public function __construct(callable $callback)
	{
		$this->callback = $callback;
	}

	public function setErrorHandler(IExceptionHandler $handler): void
	{
		$this->errorHandler = $handler;
	}
	

	public function get($queueName): ILocker
	{
		/** @var ILocker $locker */
		$locker = ($this->callback)($queueName);
		$locker->setErrorHandler($this->errorHandler);
		
		return $locker;
	}

	public function setTTL(int $ttl): void
	{
		return;
	}
}