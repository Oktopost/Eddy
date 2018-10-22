<?php
namespace Eddy\Base\Engine\Lock;


use Eddy\Base\IExceptionHandler;


interface ILocker
{
	public function setErrorHandler(IExceptionHandler $handler): void ;
	
	public function lock(): bool;
	public function isLocked(): bool;
	public function unlock(): bool;
}