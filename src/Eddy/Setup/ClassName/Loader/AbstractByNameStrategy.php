<?php
namespace Eddy\Setup\ClassName\Loader;


use Eddy\Base\IEddyQueueObject;
use Eddy\Base\Setup\ClassName\ILoaderStrategy;
use Eddy\Utils\ClassNameSearch;


abstract class AbstractByNameStrategy implements ILoaderStrategy
{
	public const CONFIG_SUFFIX	= 'Config';
	
	
	private $suffix;
	
	/** @var ConfigObjectLoaderStrategy */
	private $loader;
	
	
	protected abstract function validate(string $item, IEddyQueueObject $config);
	
	
	public function __construct(string $type, string $suffix)
	{
		$this->loader = new ConfigObjectLoaderStrategy($type);
		$this->suffix = $suffix;
	}


	public function tryLoad(string $item): ?IEddyQueueObject
	{
		$configName = ClassNameSearch::find($item, $this->suffix, self::CONFIG_SUFFIX);
		
		if (!class_exists($configName))
			return null;
		
		$config = $this->loader->tryLoad($configName);
		
		if ($config)
		{
			$this->validate($item, $config);
		}
		
		return $config;
	}
}