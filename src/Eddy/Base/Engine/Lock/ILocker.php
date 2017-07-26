<?php
namespace Eddy\Base\Engine\Lock;


interface ILocker
{
	public function lock(float $timeoutSeconds = -1.0): bool;
	public function isLocked(): bool;
	public function unlock();
}