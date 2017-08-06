<?php
namespace Eddy\Plugins\Utils\LockProviders;


use Eddy\Base\Engine\ILockProvider;
use Eddy\Base\Engine\Lock\ILocker;


class CallbackLockProvider implements ILockProvider
{
	private $callback;
	
	
	public function __construct(callable $callback)
	{
		$this->callback = $callback;
	}


	public function get($queueName): ILocker
	{
		return ($this->callback)($queueName);
	}
}