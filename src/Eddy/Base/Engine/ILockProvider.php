<?php
namespace Eddy\Base\Engine;


use Eddy\Base\Engine\Lock\ILocker;


interface ILockProvider
{
	public function get($queueName): ILocker;
	public function setTTL(int $ttl): void;
}