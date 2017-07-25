<?php
namespace Eddy\Object;


use Eddy\Enums\EventState;

use Objection\LiteSetup;
use Objection\LiteObject;


/**
 * @property string $Id
 * @property string $Created
 * @property string $Modified
 * @property string	$Name
 * @property string	$State
 * @property string	$EventInterface
 * @property string	$ProxyClassName
 * @property string	$ConfigClassName
 * @property string	$HandlerInterface
 * @property float	$Delay
 * @property int	$MaxBulkSize
 */
class EventObject extends LiteObject
{
	protected function _setup()
	{
		return [
			'Id'				=> LiteSetup::createString(),
			'Created'			=> LiteSetup::createString(),
			'Modified'			=> LiteSetup::createString(),
			'Name'				=> LiteSetup::createString(),
			'State'				=> LiteSetup::createString(EventState::RUNNING),
			'EventInterface'	=> LiteSetup::createString(),
			'ProxyClassName'	=> LiteSetup::createString(null),
			'ConfigClassName'	=> LiteSetup::createString(null),
			'HandlerInterface'	=> LiteSetup::createString(),
			'Delay'				=> LiteSetup::createDouble(),
			'MaxBulkSize'		=> LiteSetup::createInt(255)
		];
	}
}