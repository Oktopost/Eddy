<?php
namespace Eddy\Utils;


use Eddy\Base\Config\INaming;

use Objection\LiteSetup;
use Objection\LiteObject;


/**
 * @property string	$MainPrefix
 * @property string $EventQueuePrefix
 * @property string $HandlerQueuePrefix
 * @property string $LockPrefix
 * @property string $MainQueueName
 */
class Naming extends LiteObject implements INaming
{
	/**
	 * @return array
	 */
	protected function _setup()
	{
		return [
			'MainPrefix'			=> LiteSetup::createString('Eddy:'),
			'EventQueuePrefix'		=> LiteSetup::createString('Eddy:Event:'),
			'HandlerQueuePrefix'	=> LiteSetup::createString('Eddy:Handler:'),
			'LockPrefix'			=> LiteSetup::createString('Eddy:Lock:'),
			'MainQueueName'			=> LiteSetup::createString('Eddy:Main')
		];
	}
}