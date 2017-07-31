<?php
namespace Eddy\Plugins\StatisticsCollector\Enum;


class StatsStatus
{
	use \Objection\TEnum;
	
	
	public const SUCCESS	= 'success';
	public const ERROR		= 'error';
}