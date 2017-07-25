<?php
namespace Eddy\Base\Engine;


interface ILocker
{
	public function lock(string $eventName, int $timeoutSeconds = -1): bool;
	public function unlock(string $eventName): bool;
}