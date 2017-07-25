<?php
namespace Eddy\Base\Engine\Lock;


interface ILocker
{
	public function lock(string $eventName, float $timeoutSeconds = -1.0): bool;
	public function unlock(string $eventName);
}