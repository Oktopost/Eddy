<?php
namespace Eddy\Setup;


use Eddy\Base\Setup\IEventsSetup;
use Eddy\Objects\EventObject;
use Eddy\Objects\HandlerObject;

use Objection\LiteSetup;
use Objection\LiteObject;


class EventSetup extends LiteObject implements IEventsSetup
{
	/**
	 * @return array
	 */
	protected function _setup()
	{
		return [
			'Handlers'		=> LiteSetup::createInstanceArray(HandlerObject::class),
			'Events'		=> LiteSetup::createInstanceArray(EventObject::class),
			'Subscribers'	=> LiteSetup::createArray()
		];
	}
	
	
	public function addSubscriber(string $event, string $handler): void
	{
		if (!isset($this->Subscribers[$event]))
		{
			$this->Subscribers[$event] = [$handler];
		}
		else
		{
			$this->Subscribers[$event][] = $handler;
		}
	}
}