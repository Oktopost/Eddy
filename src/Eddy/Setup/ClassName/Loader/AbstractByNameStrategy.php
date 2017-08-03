<?php
namespace Eddy\Setup\ClassName\Loader;


use Eddy\Base\IEddyQueueObject;
use Eddy\Base\Setup\ClassName\ILoaderStrategy;


abstract class AbstractByNameStrategy implements ILoaderStrategy
{
	public const CONFIG_SUFFIX	= 'Config';
	
	
	private $suffix;
	
	/** @var ConfigObjectLoaderStrategy */
	private $loader;
	
	
	private function replaceNameSuffix(string $source, string $with = ''): string
	{
		$length = strlen($this->suffix);
		$sourceLength = strlen($source);
		
		if (substr($source, $sourceLength - $length) == $this->suffix)
		{
			$source = substr($source, 0, $sourceLength - $length);
		}
		
		return $source . $with;
	}
	
	
	protected abstract function validate(string $item, IEddyQueueObject $config);
	
	
	public function __construct(string $type, string $suffix)
	{
		$this->loader = new ConfigObjectLoaderStrategy($type);
		$this->suffix = $suffix;
	}


	public function tryLoad(string $item): ?IEddyQueueObject
	{
		$configName = $this->replaceNameSuffix($item, self::CONFIG_SUFFIX);
		
		if (!class_exists($configName))
		{
			return null;
		}
		
		$config = $this->loader->tryLoad($configName);
		
		if ($config)
		{
			$this->validate($item, $config);
		}
		
		return $config;
	}
}