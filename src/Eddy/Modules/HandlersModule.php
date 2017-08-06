<?php
namespace Eddy\Modules;


use Eddy\Base\Module\IHandlersModule;
use Eddy\Enums\EventState;
use Eddy\Object\HandlerObject;


/**
 * @context
 */
class HandlersModule implements IHandlersModule
{
	/**
	 * @context
	 * @var \Eddy\Base\IConfig
	 */
	private $config;
	
	
	public function pause(HandlerObject $object): void
	{
		$object->State = EventState::PAUSED;
		
		$this->config->DAL()->handlers()->updateSettings($object);
	}
}