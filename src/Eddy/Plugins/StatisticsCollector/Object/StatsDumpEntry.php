<?php
namespace Eddy\Plugins\StatisticsCollector\Object;


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
class StatsDumpEntry extends LiteObject
{
	protected function _setup()
	{
		return [
			'Name'		=> LiteSetup::createString(),
			'Type'		=> LiteSetup::createString(),
			'Operation'	=> LiteSetup::createString(),
			'Amount'	=> LiteSetup::createInt(),
			'StartTime'	=> LiteSetup::createInt(),
			'EndTime'	=> LiteSetup::createInt()
		];
	}
}