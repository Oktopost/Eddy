<?php
namespace Eddy\Setup\ClassName;


use Eddy\Base\Setup\ClassName\IHandlerBuilder;
use Eddy\Base\Setup\ClassName\ILoaderStrategy;

use Eddy\IHandlerConfig;
use Eddy\Object\HandlerObject;


class HandlerBuilder implements IHandlerBuilder
{
	/** @var ILoaderStrategy[] */
	private $loaders;
	
	
	public function __construct()
	{
		$this->loaders = [
			new Loader\ConfigObjectLoaderStrategy(IHandlerConfig::class),
			new Loader\HandlerAnnotationStrategy(),
			new Loader\ByHandlerNameStrategy()
		];
	}
	
	
	public function tryBuild(string $item): ?HandlerObject
	{
		foreach ($this->loaders as $loader)
		{
			/** @var HandlerObject $result */
			$result = $loader->tryLoad($item);
			
			if ($result) return $result;
		}
		
		return null;
	}
}