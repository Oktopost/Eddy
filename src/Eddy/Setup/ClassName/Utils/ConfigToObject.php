<?php
namespace Eddy\Setup\ClassName\Utils;


use Eddy\IEventConfig;
use Eddy\IHandlerConfig;
use Eddy\Base\IEddyQueueObject;
use Eddy\Objects\EventObject;
use Eddy\Objects\HandlerObject;

use Objection\TStaticClass;


class ConfigToObject
{
	use TStaticClass;
	
	
	private static function getEventObject(IEventConfig $config): EventObject
	{
		$object = new EventObject();
		
		$object->Name			= $config->name();
		$object->Delay			= $config->delay();
		$object->State			= $config->initialState();
		$object->MaxBulkSize	= $config->maxBulkSize();
		$object->DelayBuffer	= $config->delayBuffer();
		$object->PackageSize	= $config->packageSize();
		
		$object->ConfigClassName	= get_class($config);
		$object->ProxyClassName		= $config->proxyClassName();
		$object->HandlerInterface	= $config->handlersInterface();
		$object->EventInterface		= $config->eventClassName();
		
		return $object;
	}
	
	private static function getHandlerObject(IHandlerConfig $config): HandlerObject
	{
		$object = new HandlerObject();
		
		$object->Name			= $config->name();
		$object->Delay			= $config->delay();
		$object->State			= $config->initialState();
		$object->MaxBulkSize	= $config->maxBulkSize();
		$object->DelayBuffer	= $config->delayBuffer();
		$object->PackageSize	= $config->packageSize();
		
		$object->ConfigClassName	= get_class($config);
		$object->HandlerClassName	= $config->handlerClassName();
		
		return $object;
	}
	
	
	public static function get($item): IEddyQueueObject
	{
		if ($item instanceof IEventConfig)
		{
			return self::getEventObject($item);
		}
		else
		{
			return self::getHandlerObject($item);
		}
	}
}