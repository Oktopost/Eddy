<?php
namespace Eddy\Setup\ClassName\Loader;


use Eddy\IEventConfig;
use Eddy\Base\IEddyQueueObject;
use Eddy\Base\Setup\ClassName\ILoaderStrategy;
use Eddy\Setup\ClassName\Utils\ConfigToObject;


class ConfigObjectLoaderStrategy implements ILoaderStrategy
{
	private $targetInterface;
	
	
	public function __construct(string $targetInterface)
	{
		$this->targetInterface = $targetInterface;
	}


	public function tryLoad(string $item): ?IEddyQueueObject
	{
		if (!is_subclass_of($item, $this->targetInterface)) return null;
		
		$reflection = new \ReflectionClass($item);
		
		if ($reflection->isAbstract())
			return null;
		
		$constructor = $reflection->getConstructor();
		
		if ($constructor)
		{
			if (!$constructor->isPublic() ||
				$constructor->getNumberOfRequiredParameters() > 0)
			{
				return null;
			}
		}
			
		/** @var IEventConfig $config */
		$config = $reflection->newInstance();
		
		return ConfigToObject::get($config);
	}
}