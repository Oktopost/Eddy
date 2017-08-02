<?php
namespace Eddy\Setup\ClassName\Loader;


use Eddy\Base\IEddyQueueObject;
use Eddy\IEventConfig;
use Eddy\Object\EventObject;

use Eddy\Exceptions\ConfigMismatchException;


class EventAnnotationStrategy extends AbstractAnnotationStrategy
{
	protected function validate(string $item, IEddyQueueObject $config)
	{
		/** @var EventObject $config */
		if ($config->EventInterface != $item)
		{
			throw new ConfigMismatchException(
				"The configuration defined by annotation for the interface $item, points " . 
					"to a different interface {$config->EventInterface}. In config: {$config->ConfigClassName}", 
				305);
		}
	}
	
	
	public function __construct()
	{
		parent::__construct(IEventConfig::class);
	}
}