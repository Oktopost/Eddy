<?php

namespace Eddy\Setup\ClassName\Loader;


use Eddy\ObjectAnnotations;
use Eddy\Base\IEddyQueueObject;
use Eddy\Base\Setup\ClassName\ILoaderStrategy;

use Eddy\Exceptions\ConfigMismatchException;


abstract class AbstractAnnotationStrategy implements ILoaderStrategy
{
	/** @var ConfigObjectLoaderStrategy */
	private $loader;
	
	
	protected abstract function validate(string $item, IEddyQueueObject $config);
	
	
	public function __construct(string $type)
	{
		$this->loader = new ConfigObjectLoaderStrategy($type);
	}
	
	public function tryLoad(string $item): ?IEddyQueueObject
	{
		$configName = ObjectAnnotations::getConfigName($item);
		
		if (is_null($configName))
		{
			return null;
		}
		
		if (!class_exists($configName))
		{
			throw new ConfigMismatchException(
				"The configuration defined by annotation for $item, doesn't exists: $configName",
				303);
		}
		
		$config = $this->loader->tryLoad($configName);
		
		if ($config)
		{
			$this->validate($item, $config);
		}
		
		return $config;
	}
}