<?php
namespace Eddy\Plugins\StatisticsCollector\Object;


use Eddy\Plugins\StatisticsCollector\Enum\StatsStatus;
use Eddy\Plugins\StatisticsCollector\Enum\StatsOperation;
use Eddy\Plugins\StatisticsCollector\Enum\StatsObjectType;

use Objection\LiteSetup;
use Objection\LiteObject;


/**
 * @property string	$Name
 * @property string	$Type
 * @property string	$Operation
 * @property string	$Status
 * @property int	$Amount
 * @property int	$Time
 */
class StatsCachedEntry extends LiteObject
{
	protected function _setup()
	{
		return [
			'Name'			=> LiteSetup::createString(),
			'Type'			=> LiteSetup::createEnum(StatsObjectType::class),
			'Operation'		=> LiteSetup::createEnum(StatsOperation::class),
			'Status'		=> LiteSetup::createEnum(StatsStatus::class),
			'Amount'		=> LiteSetup::createInt(),
			'DataTime'		=> LiteSetup::createInt(),
			'TotalRuntime'	=> LiteSetup::createDouble(),
		];
	}
}