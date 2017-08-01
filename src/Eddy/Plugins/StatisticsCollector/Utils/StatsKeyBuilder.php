<?php
namespace Eddy\Plugins\StatisticsCollector\Utils;


use Eddy\Plugins\StatisticsCollector\Object\StatsEntry;


class StatsKeyBuilder
{
	use \Objection\TStaticClass;
	
	
	public static function get(StatsEntry $entry, int $time): string
	{
		return "{$entry->Type}:{$entry->Name}:{$time}";
	}
}