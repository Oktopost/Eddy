<?php
namespace Eddy\Setup\ClassName;


use Eddy\Base\Setup\ClassName\IEventBuilder;
use Eddy\Base\Setup\ClassName\ILoaderStrategy;

use Eddy\IEventConfig;
use Eddy\Objects\EventObject;


class EventBuilder implements IEventBuilder
{
	/** @var ILoaderStrategy[] */
	private $loaders;
	
	
	public function __construct()
	{
		$this->loaders = [
			new Loader\ConfigObjectLoaderStrategy(IEventConfig::class),
			new Loader\EventAnnotationStrategy(),
			new Loader\ByEventNameStrategy()
		];
	}


	public function tryBuild(string $item): ?EventObject
	{
		foreach ($this->loaders as $loader)
		{
			/** @var EventObject|null $result */
			$result = $loader->tryLoad($item);
			
			if ($result) return $result;
		}
		
		return null;
	}
}