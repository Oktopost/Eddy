<?php
namespace Eddy\Plugins\StatisticsCollector\Enum;


class StatsObjectType
{
	use \Objection\TEnum;
	
	
	public const EVENT		= 'Event';
	public const HANDLER	= 'Handler';
}