<?php
namespace Eddy\Object;


use Eddy\Base\IEddyQueueObject;
use Eddy\Base\Config\INaming;
use Eddy\Enums\EventState;

use Objection\LiteSetup;
use Objection\LiteObject;


/**
 * @property string	$Id
 * @property string $Created
 * @property string $Modified
 * @property string	$Name
 * @property string	$State
 * @property string	$HandlerClassName,
 * @property string	$ConfigClassName,
 * @property float	$Delay,
 * @property int	$MaxBulkSize
 * @property float	$DelayBuffer
 * @property int	$PackageSize
 */
class HandlerObject extends AEddyObject implements IEddyQueueObject
{
	protected function _setup()
	{
		return [
			'Id'				=> LiteSetup::createString(),
			'Created'			=> LiteSetup::createString(),
			'Modified'			=> LiteSetup::createString(),
			'Name'				=> LiteSetup::createString(),
			'State'				=> LiteSetup::createString(EventState::RUNNING),
			'HandlerClassName'	=> LiteSetup::createString(),
			'ConfigClassName'	=> LiteSetup::createString(null),
			'Delay'				=> LiteSetup::createDouble(),
			'MaxBulkSize'		=> LiteSetup::createInt(255),
			'DelayBuffer'		=> LiteSetup::createDouble(),
			'PackageSize'		=> LiteSetup::createInt()
		];
	}
	
	
	public function getQueueNaming(INaming $naming): string
	{
		return $naming->HandlerQueuePrefix . $this->Name;
	}
}