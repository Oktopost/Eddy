<?php
namespace Eddy\Modules;


use Eddy\Scope;
use Eddy\Base\Module\IEventModule;
use Eddy\Base\Setup\IClassNameLoader;
use Eddy\Object\EventObject;
use Eddy\Exceptions\InvalidEventException;


/**
 * @context
 */
class EventModule implements IEventModule
{
	/**
	 * @context 
	 * @var \Eddy\Base\IConfig 
	 */
	private $config;
	
	
	public function loadByInterfaceName(string $interfaceName): EventObject
	{
		$eventObject = $this->config->DAL()->events()->loadByInterfaceName($interfaceName);
		
		if (!$eventObject)
		{
			/** @var IClassNameLoader $loader */
			$loader = Scope::skeleton(IClassNameLoader::class);
			
			$eventObject = $loader->loadEvent($interfaceName);
		}
		
		if (!$eventObject)
		{
			throw new InvalidEventException($interfaceName);
		}
		
		return $eventObject;
	}
}