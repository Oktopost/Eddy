<?php
namespace Eddy\Plugins;


use Eddy\IEddyPlugin;
use Eddy\Utils\Config;
use Eddy\Plugins\DoNotForget\ITarget;
use Eddy\Plugins\PublishLater\PublishLaterEvent;
use Eddy\Exceptions\InvalidUsageException;


class PublishLaterPlugin implements ITarget, IEddyPlugin
{
	/** @var Config */
	private $config;
	
	/** @var PublishLaterEvent[] */
	private $stack = [];
	
	private $isFlushing = false;
	
	
	public function mock($event, string $name): PublishLaterEvent
	{
		if ($this->isFlushing)
		{
			throw new InvalidUsageException('Can not mock inside flushing');
		}
		
		if (!isset($this->stack[$name]))
		{
			$mock = new PublishLaterEvent($event);
			$this->stack[$name] = $mock;
		}
		
		return $this->stack[$name];
	}
	
	
	public function flush()
	{
		$this->isFlushing = true;
		
		foreach ($this->stack as $publishObject)
		{
			$original = $publishObject->original();
			
			foreach ($publishObject->getStack() as $name => $allArgs)
			{
				foreach ($allArgs as $callArgs)
				{
					try
					{
						call_user_func_array([$original, $name], $callArgs);
					}
					catch (\Throwable $t)
					{
						$this->config->handleError($t);
					}
				}
			}
		}
		
		$this->stack = [];
		
		$this->isFlushing = false;
	}

	public function setup(Config $config)
	{
		$this->config = $config;
		
		$flush = $this;
		
		$doNotForget = new DoNotForgetPlugin();
		$doNotForget->to($flush);
		
		return $doNotForget;
	}
}