<?php
namespace Eddy\Setup\ClassName\Loader;


use Eddy\Base\IEddyQueueObject;
use Eddy\Base\Setup\ClassName\ILoaderStrategy;
use Eddy\Exceptions\ConfigMismatchException;

use Eddy\ObjectAnnotations;


abstract class AbstractByNameStrategy implements ILoaderStrategy
{
	public const EVENT_SUFFIX	= 'Event';
	public const HANDLER_SUFFIX	= 'Handler';
	public const CONFIG_SUFFIX	= 'Config';
	
	
	private $suffix;
	
	/** @var ConfigObjectLoaderStrategy */
	private $loader;
	
	
	protected abstract function validate(string $item, IEddyQueueObject $config);
	
	
	public function __construct(string $type, string $suffix)
	{
		$this->loader = new ConfigObjectLoaderStrategy($type);
	}


	public function tryLoad(string $item): ?IEddyQueueObject
	{
		if ((!ObjectAnnotations::isEvent($item) && !ObjectAnnotations::isHandler($item)) ||  
			substr($item, strlen($item) - strlen($this->suffix)) != $this->suffix)
		{
			return null;
		}
		
		$configName = substr($item, 0, strlen($item) - strlen($this->suffix)) . self::CONFIG_SUFFIX;
		
		if (!class_exists($configName))
		{
			throw new ConfigMismatchException(
				"The configuration matching $item by name, doesn't exists: $configName",
				300);
		}
		
		$config = $this->loader->tryLoad($item);
		
		if ($config)
		{
			$this->validate($item, $config);
		}
		
		return $config;
	}
}