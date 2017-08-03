<?php
namespace Eddy\Setup\ClassName\Loader;


use Eddy\ObjectAnnotations;
use Eddy\Base\Setup\ClassName\ILoaderStrategy;


abstract class AbstractLoaderStrategy implements ILoaderStrategy
{
	public const EVENT_SUFFIX	= 'Event';
	public const HANDLER_SUFFIX	= 'Handler';
	public const CONFIG_SUFFIX	= 'Config';
	
	
	protected function isEventOrHandler(string $item): bool
	{
		if (!ObjectAnnotations::isEvent($item) && 
			!ObjectAnnotations::isHandler($item) &&  
			substr($item, strlen($item) - strlen(self::EVENT_SUFFIX)) != self::EVENT_SUFFIX &&
			substr($item, strlen($item) - strlen(self::HANDLER_SUFFIX)) != self::HANDLER_SUFFIX)
		{
			return false;
		}
		
		return true;
	}
}