<?php
namespace Eddy\Base\Engine\Lock;


interface ILocker
{
	public function lock(): bool;
	public function isLocked(): bool;
	public function unlock(): bool;
}