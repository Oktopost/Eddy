<?php
namespace Eddy\Modules;


use Eddy\Scope;
use Eddy\Base\Module\IEventModule;
use Eddy\Base\Setup\IClassNameLoader;
use Eddy\Objects\EventObject;
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


	private function getEventFromLoader(string $interfaceName): ?EventObject
	{
		/** @var IClassNameLoader $loader */
		$loader = Scope::skeleton(IClassNameLoader::class);
		
		$eventObject = $loader->loadEvent($interfaceName);
		
		if ($eventObject)
		{
			$this->config->DAL()->events()->saveSetup($eventObject);
		}
		
		return $eventObject;
	}
	
	
	public function loadByInterfaceName(string $interfaceName): EventObject
	{
		$eventObject = $this->config->DAL()->events()->loadByInterfaceName($interfaceName);
		
		if (!$eventObject)
		{
			$eventObject = $this->getEventFromLoader($interfaceName);
		}
		
		if (!$eventObject)
		{
			throw new InvalidEventException($interfaceName);
		}
		
		return $eventObject;
	}
}