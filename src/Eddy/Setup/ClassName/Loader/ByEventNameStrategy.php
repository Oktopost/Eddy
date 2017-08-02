<?php
namespace Eddy\Setup\ClassName\Loader;


use Eddy\IEventConfig;
use Eddy\Base\IEddyQueueObject;
use Eddy\Object\EventObject;
use Eddy\Exceptions\ConfigMismatchException;


class ByEventNameStrategy extends AbstractByNameStrategy
{
	protected function validate(string $item, IEddyQueueObject $config)
	{
		/** @var EventObject $config */
		if ($config->EventInterface != $item)
		{
			throw new ConfigMismatchException(
				"The configuration defined for the interface $item, points " . 
					"to a different interface {$config->EventInterface}. In config: {$config->ConfigClassName}", 
				301);
		}
	}
	
	
	public function __construct()
	{
		parent::__construct(IEventConfig::class, self::EVENT_SUFFIX);
	}
}