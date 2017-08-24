<?php
namespace Eddy\Plugins;


use Eddy\IEddyPlugin;
use Eddy\Base\IEddyQueueObject;
use Eddy\Base\Engine\Processor\AProcessController;
use Eddy\Base\Engine\Processor\IProcessController;

use Eddy\Utils\Config;
use Eddy\Object\HandlerObject;
use Eddy\Plugins\DoNotForget\ITarget;


class DoNotForgetPlugin extends AProcessController implements IEddyPlugin, IProcessController
{
	/** @var callable[] */
	private $todo = [];
	
	/** @var Config */
	private $config;
	
	
	private function execute(): void
	{
		foreach ($this->todo as $item)
		{
			try
			{
				$item();
			}
			catch (\Throwable $e)
			{
				$this->config->handleError($e);
			}
		}
	}


	/**
	 * @param callable|ITarget $callback
	 * @return DoNotForgetPlugin
	 */
	public function to($callback): DoNotForgetPlugin
	{
		if ($callback instanceof ITarget)
		{
			$callback = function () use ($callback) { $callback->flush(); };
		}
		
		$this->todo[] = $callback;
		return $this;
	}
	
	public function setup(Config $config)
	{
		$this->config = $config;
		$config->Engine->addController($this);
	}
	
	
	public function postProcess(IEddyQueueObject $target, array $payload): void
	{
		$this->execute();
	}
	
	public function exception(HandlerObject $target, array $payload, \Throwable $t): bool
	{
		$this->execute();
		return false;
	}
}