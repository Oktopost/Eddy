<?php
namespace Eddy\Plugins\StatisticsCollector\Object;


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
class StatsEntry extends LiteObject
{
	protected function _setup()
	{
		return [
			'Name'		=> LiteSetup::createString(),
			'Type'		=> LiteSetup::createString(),
			'Operation'	=> LiteSetup::createString(),
			'Status'	=> LiteSetup::createString(),
			'Amount'	=> LiteSetup::createInt(),
			'Time'		=> LiteSetup::createInt()
		];
	}
}