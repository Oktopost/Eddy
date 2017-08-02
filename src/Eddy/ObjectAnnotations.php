<?php
namespace Eddy;


use Annotation\Flag;
use Annotation\Value;


class ObjectAnnotations
{
	use \Objection\TStaticClass;
	
	
	public const EVENT_ANNOTATION	= 'Event';
	public const PROXY_ANNOTATION	= 'Proxy';
	public const HANDLER_ANNOTATION	= 'Handler';
	public const CONFIG_ANNOTATION	= 'Config';
	
	
	public static function isEvent($target): bool
	{
		return Flag::hasFlag($target, self::EVENT_ANNOTATION);
	}
	
	public static function getEventName($target): ?string
	{
		return Value::getValue($target, self::EVENT_ANNOTATION, null);
	}
	
	public static function isProxy($target): bool
	{
		return Flag::hasFlag($target, self::PROXY_ANNOTATION);
	}
	
	public static function isHandler($target): bool
	{
		return Flag::hasFlag($target, self::HANDLER_ANNOTATION);
	}
	
	public static function getConfigName($target): ?string
	{
		return Value::getValue($target, self::CONFIG_ANNOTATION, null);
	}
}