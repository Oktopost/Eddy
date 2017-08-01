<?php
namespace Eddy\Plugins\StatisticsCollector\Object;


use Eddy\Plugins\StatisticsCollector\Enum\StatsObjectType;

use Objection\LiteSetup;
use Objection\LiteObject;


/**
 * @property string	$Name
 * @property string	$Type
 * @property string	$Operation
 * @property int	$Amount
 * @property int	$StartDate
 * @property int	$EndDate
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