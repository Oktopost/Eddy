<?php
namespace Eddy\Base\Engine\Processor;


use Eddy\Base\IEddyQueueObject;

use Objection\LiteSetup;
use Objection\LiteObject;


/**
 * @property IEddyQueueObject 	$Object
 * @property array				$Payload
 */
class ProcessTarget extends LiteObject
{
	/**
	 * @return array
	 */
	protected function _setup()
	{
		return [
			'Object'	=> LiteSetup::createInstanceOf(IEddyQueueObject::class),
			'Payload'	=> LiteSetup::createArray()
		];
	}
}