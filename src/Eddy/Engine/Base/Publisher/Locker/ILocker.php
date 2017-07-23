<?php
namespace Eddy\Engine\Base\Publisher\Locker;


interface ILocker
{
	public function lock(string $eventName): bool;
	public function unlock(string $eventName): bool;
}