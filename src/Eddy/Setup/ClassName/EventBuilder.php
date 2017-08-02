<?php
namespace Eddy\Setup\ClassName;


use Eddy\Base\Setup\ClassName\IEventLoader;
use Eddy\Base\Setup\ClassName\IEventBuilder;

use Eddy\Object\EventObject;


class EventBuilder implements IEventBuilder
{
	/** @var IEventLoader[] */
	private $loaders;
	
	
	private function __construct()
	{
		$this->loaders = [
			
		];
	}


	public function tryBuild($item): ?EventObject
	{
		foreach ($this->loaders as $loader)
		{
			$result = $loader->tryLoad($item);
			
			if ($result) return $result;
		}
		
		return null;
	}
}