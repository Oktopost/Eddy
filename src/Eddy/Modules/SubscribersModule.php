<?php
namespace Eddy\Modules;


use Eddy\Objects\EventObject;
use Eddy\Base\Module\ISubscribersModule;


/**
 * @context
 */
class SubscribersModule implements ISubscribersModule
{
	/**
	 * @context
	 * @var \Eddy\Base\IConfig
	 */
	private $config;
	
	
	public function get(EventObject $eventObject): array
	{
		if (!$eventObject->Id)
			return [];
		
		$handlerIds = $this->config->DAL()->subscribers()->getHandlersIds($eventObject->Id);
		
		if (!$handlerIds)
			return [];
		
		return $this->config->DAL()->handlers()->loadMultiple($handlerIds);
	}
}