<?php
namespace Eddy\Plugins\StatisticsCollector\Enum;


class StatsOperation
{
	use \Objection\TEnum;
	
	
	public const ENQUEUE			= 'enqueue';
	public const DEQUEUE			= 'dequeue';
	
	public const ERROR				= 'error';
	public const EXECUTION_TIME 	= 'execution-time';
}