<?php
namespace Eddy\Plugins\StatisticsCollector\Utils;


use Eddy\Plugins\StatisticsCollector\Object\StatsCachedEntry;


class StatsKeyBuilder
{
	use \Objection\TStaticClass;
	
	
	public static function get(StatsCachedEntry $entry): string
	{
		return "{$entry->Type}:{$entry->Name}:{$entry->Time}";
	}
}