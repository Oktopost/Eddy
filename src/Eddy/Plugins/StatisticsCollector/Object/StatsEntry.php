<?php
namespace Eddy\Plugins\StatisticsCollector\Object;


use Eddy\Plugins\StatisticsCollector\Enum\StatsObjectType;

use Objection\LiteSetup;
use Objection\LiteObject;


/**
 * @property string	$Name
 * @property string	$Type
 * @property int	$Enqueued
 * @property int	$Dequeued
 * @property int	$ErrorsCount
 * @property int	$Processed
 * @property double	$TotalRuntime
 * @property int	$Granularity
 * @property string	$DataDate
 */
class StatsEntry extends LiteObject
{
	protected function _setup()
	{
		return [
			'Name'			=> LiteSetup::createString(),
			'Type'			=> LiteSetup::createEnum(StatsObjectType::class),
			'Enqueued'		=> LiteSetup::createInt(),
			'Dequeued'		=> LiteSetup::createInt(),
			'ErrorsCount'	=> LiteSetup::createInt(),
			'Processed'		=> LiteSetup::createInt(),
			'TotalRuntime'	=> LiteSetup::createDouble(),
			'Granularity'	=> LiteSetup::createInt(),
			'DataDate'		=> LiteSetup::createString()
		];
	}
}