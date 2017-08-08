<?php
namespace Eddy\Object;


use Eddy\IEventConfig;
use Eddy\Base\IEddyQueueObject;
use Eddy\Base\Config\INaming;
use Eddy\Enums\EventState;
use Eddy\Event\AnonymousObjectEventConfig;

use Eddy\Exceptions\ConfigMismatchException;

use Objection\LiteSetup;


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
 * @property float	$DelayBuffer
 * @property int	$PackageSize
 */
class EventObject extends AEddyObject implements IEddyQueueObject
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
			'MaxBulkSize'		=> LiteSetup::createInt(255),
			'DelayBuffer'		=> LiteSetup::createDouble(),
			'PackageSize'		=> LiteSetup::createInt()
		];
	}
	
	
	public function getQueueNaming(INaming $naming): string
	{
		return $naming->EventQueuePrefix . $this->Name;
	}
	
	public function getConfig(): IEventConfig
	{
		if (!$this->ConfigClassName)
		{
			return new AnonymousObjectEventConfig($this);
		}
		else if (!class_exists($this->ConfigClassName))
		{
			throw new ConfigMismatchException("The configuration class {$this->ConfigClassName} for event " . 
				"$this->Id, $this->Name, does not exists!");
		}
		else
		{
			$className = $this->ConfigClassName; 
			return new $className();
		}
	}
}