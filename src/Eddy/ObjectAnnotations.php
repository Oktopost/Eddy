<?php
namespace Eddy;


use Annotation\Flag;
use Annotation\Value;


class ObjectAnnotations
{
	use \Objection\TStaticClass;
	
	
	public const EVENT_ANNOTATION			= 'Event';
	public const PROXY_ANNOTATION			= 'Proxy';
	public const HANDLER_ANNOTATION			= 'Handler';
	public const CONFIG_ANNOTATION			= 'Config';
	public const UNIQUE_ANNOTATION			= 'Unique';
	public const DELAY_BUFFER_ANNOTATION 	= 'DelayBuffer';
	public const PACKAGE_SIZE_ANNOTATION	= 'PackageSize';
	
	
	public static function isEvent($target): bool
	{
		return Flag::hasFlag($target, self::EVENT_ANNOTATION);
	}
	
	public static function getEventName($target): ?string
	{
		return Value::getValue($target, self::EVENT_ANNOTATION, null);
	}
	
	public static function isUnique($target): bool
	{
		return Flag::hasFlag($target, self::UNIQUE_ANNOTATION);
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
	
	public static function getDelayBuffer($target): ?float
	{
		return Value::getValue($target, self::DELAY_BUFFER_ANNOTATION, null);
	}
	
	public static function getPackageSize($target): ?int
	{
		return Value::getValue($target, self::PACKAGE_SIZE_ANNOTATION, null);
	}
}