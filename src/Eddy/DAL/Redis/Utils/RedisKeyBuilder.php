<?php
namespace Eddy\DAL\Redis\Utils;


class RedisKeyBuilder
{
	use \Objection\TStaticClass;
	
	
	private const HANDLER_OBJECTS 		= 'HandlerObjects';
	private const HANDLER_BY_NAME		= 'HandlerObjectsByName';
	private const HANDLER_BY_CLASSNAME	= 'HandlerObjectsByClassName';
	
	private const EVENT_OBJECTS			= 'EventObjects';
	private const EVENT_BY_NAME			= 'EventObjectsByName';
	private const EVENT_BY_INTERFACE	= 'EventObjectsByInterface';
	
	private const EVENT_HANDLERS_PREFIX	= 'EventHandlers:';
	private const EXECUTOR_PREFIX		= 'EventExecutor:';

	private const HANDLER_EVENTS_PREFIX	= 'HandlerEvents:';
	
	
	public static function handlerObject(): string
	{
		return self::HANDLER_OBJECTS;
	}
	
	public static function handlerByName(): string
	{
		return self::HANDLER_BY_NAME;
	}
	
	public static function handlerByClassName(): string 
	{
		return self::HANDLER_BY_CLASSNAME;
	}
	
	public static function eventObject(): string
	{
		return self::EVENT_OBJECTS;
	}
	
	public static function eventByName(): string
	{
		return self::EVENT_BY_NAME;
	}
	
	public static function eventByInterface(): string 
	{
		return self::EVENT_BY_INTERFACE;
	}
	
	public static function eventHandlers(string $eventId): string 
	{
		return self::EVENT_HANDLERS_PREFIX . $eventId;
	}
	
	public static function handlerEvents(string $handlerId): string 
	{
		return self::HANDLER_EVENTS_PREFIX . $handlerId;
	}
	
	public static function executorsKey(string $handlerId): string 
	{
		return self::EXECUTOR_PREFIX . $handlerId;
	}
	
	public static function getEventHandlersPrefix(): string 
	{
		return self::EVENT_HANDLERS_PREFIX;
	}
	
	public static function getHandlerEventsPrefix(): string 
	{
		return self::HANDLER_EVENTS_PREFIX;
	}
	
	public static function getExecutorsPrefix(): string
	{
		return self::EXECUTOR_PREFIX;
	}
}