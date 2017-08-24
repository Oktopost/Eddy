<?php
namespace Eddy\Plugins;


use Eddy\IEddyPlugin;
use Eddy\Utils\Config;
use Eddy\Plugins\DoNotForget\ITarget;
use Eddy\Plugins\PublishLater\PublishLaterEvent;


class PublishLaterPlugin implements ITarget, IEddyPlugin
{
	/** @var Config */
	private $config;
	
	/** @var PublishLaterEvent[] */
	private $stack = [];
	
	
	public function mock($event): PublishLaterEvent
	{
		$mock = new PublishLaterEvent($event);
		$this->stack[] = $mock;
		return $mock;
	}
	
	
	public function flush()
	{
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