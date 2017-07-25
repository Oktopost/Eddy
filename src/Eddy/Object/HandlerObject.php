<?php
namespace Eddy\Object;


use Eddy\Enums\EventState;

use Objection\LiteSetup;
use Objection\LiteObject;


/**
 * @property string	$Id
 * @property string $Created
 * @property string $Modified
 * @property string	$State
 * @property string	$HandlerClassName,
 * @property float	$Delay,
 * @property int	$MaxBulkSize
 */
class HandlerObject extends LiteObject
{
	protected function _setup()
	{
		return [
			'Id'				=> LiteSetup::createString(),
			'Created'			=> LiteSetup::createString(),
			'Modified'			=> LiteSetup::createString(),
			'State'				=> LiteSetup::createString(EventState::RUNNING),
			'HandlerClassName'	=> LiteSetup::createString(),
			'ConfigClassName'	=> LiteSetup::createString(null),
			'Delay'				=> LiteSetup::createDouble(),
			'MaxBulkSize'		=> LiteSetup::createInt(255)
		];
	}
}