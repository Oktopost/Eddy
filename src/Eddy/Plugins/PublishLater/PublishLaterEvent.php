<?php
namespace Eddy\Plugins\PublishLater;


class PublishLaterEvent
{
	/** @var array */
	private $stack = [];
	
	private $original;
	
	
	public function __construct($original)
	{
		$this->original = $original;
	}


	public function original()
	{
		return $this->original;
	}
	
	public function getStack(): array 
	{
		return $this->stack;
	}
	
	
	public function __call($name, $arguments)
	{
		if (count($arguments) == 1 && is_array($arguments[0]) && 
			isset($this->stack[$name]) && count($this->stack[$name]) == 1)
		{
			$this->stack[$name][0][0] = array_merge(
				$this->stack[$name][0][0],
				$arguments[0]
			);
		}
		else
		{
			$this->stack[$name] = $this->stack[$name] ?? [];
			$this->stack[$name][] = $arguments;
		}
	}
}