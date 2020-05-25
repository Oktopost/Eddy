<?php
namespace Eddy\Setup;


use Eddy\Base\Setup\IEventsSetup;
use Eddy\Base\Setup\ISetupBuilder;
use Eddy\Objects\EventObject;
use Eddy\Objects\HandlerObject;
use Eddy\Exceptions\EddyException;


/**
 * @autoload
 */
class SetupBuilder implements ISetupBuilder
{
	/** @var EventObject[] */
	private $eventsByInterface = [];
	
	private $handlers = [];
	private $events = [];
	
	/** @var EventSetup */
	private $setup;
	
	/**
	 * @autoload 
	 * @var \Eddy\Base\Setup\IClassNameLoader 
	 */
	private $loader;
	
	
	private function getSubscribersForHandler(HandlerObject $object)
	{
		$found = false;
		$interfaces = class_implements($object->HandlerClassName);
		
		foreach ($interfaces as $interface)
		{
			if (!isset($this->eventsByInterface[$interface])) continue;
			
			$found = true;
			$event = $this->eventsByInterface[$interface];
			
			$this->setup->addSubscriber($event->Name, $object->Name);
		}
		
		if (!$found)
		{
			throw new EddyException("Handler {$object->Name} of class " . 
				"{$object->HandlerClassName} does not registered to any events!");
		}
	}
	
	private function getSubscribers()
	{
		$this->setup->Subscribers = [];
		
		foreach ($this->setup->Handlers as $handler)
		{
			$this->getSubscribersForHandler($handler);
		}
	}
	
	
	public function __construct()
	{
		$this->setup = new EventSetup();
	}


	/**
	 * @param string|array|EventObject|HandlerObject $item
	 */
	public function add($item): void
	{
		if (is_array($item))
		{
			foreach ($item as $value)
			{
				$this->add($value);
			}
			
			return;
		}
		
		if ($item instanceof EventObject)
		{
			if (isset($this->events[$item->Name]))
				return;
			
			$this->events[$item->Name] = true;
			$this->eventsByInterface[$item->HandlerInterface] = $item;
			$this->setup->Events[] = $item;
		}
		else if ($item instanceof HandlerObject)
		{
			if (isset($this->handlers[$item->Name]))
				return;
			
			$this->handlers[$item->Name] = true;
			$this->setup->Handlers[] = $item;
		}
		else
		{
			$this->add($this->loader->load($item));
		}
	}

	public function get(): IEventsSetup
	{
		$this->getSubscribers();
		return $this->setup;
	}
}