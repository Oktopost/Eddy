<?php
namespace Eddy\Base\Engine;


use Eddy\Base\Engine\Lock\ILocker;
use Eddy\Base\IExceptionHandler;


interface ILockProvider
{
	public function setErrorHandler(IExceptionHandler $handler): void;
	
	public function get($queueName): ILocker;
	public function setTTL(int $ttl): void;
}