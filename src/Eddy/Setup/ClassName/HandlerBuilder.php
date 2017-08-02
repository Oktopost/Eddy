<?php
namespace Eddy\Setup\ClassName;


use Eddy\Base\Setup\ClassName\IHandlerLoader;
use Eddy\Base\Setup\ClassName\IHandlerBuilder;

use Eddy\Object\HandlerObject;


class HandlerBuilder implements IHandlerBuilder
{
	/** @var IHandlerLoader[] */
	private $loaders;
	
	
	public function __construct()
	{
		$this->loaders = [
			
		];
	}
	
	
	public function tryBuild($item): ?HandlerObject
	{
		foreach ($this->loaders as $loader)
		{
			$result = $loader->tryLoad($item);
			
			if ($result) return $result;
		}
		
		return null;
	}
}