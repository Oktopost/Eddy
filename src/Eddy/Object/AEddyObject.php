<?php
namespace Eddy\Object;


use Eddy\Base\IEddyQueueObject;
use Eddy\Enums\EventState;

use Objection\LiteObject;


abstract class AEddyObject extends LiteObject implements IEddyQueueObject
{
	public function isActive(): bool
	{
		return in_array($this->State, EventState::ACTIVE_QUEUE_STATES);
	}
}