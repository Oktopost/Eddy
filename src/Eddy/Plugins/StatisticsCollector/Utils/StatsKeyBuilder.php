<?php
namespace Eddy\Plugins\StatisticsCollector\Utils;


class StatsKeyBuilder
{
	use \Objection\TStaticClass;

	
	public static function get(string $type, string $name, int $time): string
	{
		return "{$type}:{$name}:{$time}";
	}
	
	public static function getForCombine(string $type, string $name): string
	{
		return "{$type}.{$name}";
	}
}