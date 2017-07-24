<?php
namespace Eddy;


use Annotation\Flag;

class EventAnnotations
{
	use \Objection\TStaticClass;
	
	
	public const EVENT_ANNOTATION	= 'Event';
	public const PROXY_ANNOTATION	= 'Proxy';
	public const HANDLER_ANNOTATION	= 'Handler';
	
	
	public static function isEvent($target): bool
	{
		return Flag::hasFlag($target, self::EVENT_ANNOTATION);
	}
	
	public static function isProxy($target): bool
	{
		return Flag::hasFlag($target, self::PROXY_ANNOTATION);
	}
	
	public static function isHandler($target): bool
	{
		return Flag::hasFlag($target, self::HANDLER_ANNOTATION);
	}
}