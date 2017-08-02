<?php
namespace Eddy\Setup\ClassName\Loader;


use Eddy\IEventConfig;
use Eddy\Base\IEddyQueueObject;
use Eddy\Object\HandlerObject;

use Eddy\Exceptions\ConfigMismatchException;


class HandlerAnnotationStrategy extends AbstractAnnotationStrategy
{
	protected function validate(string $item, IEddyQueueObject $config)
	{
		/** @var HandlerObject $config */
		if ($config->HandlerClassName != $item)
		{
			throw new ConfigMismatchException(
				"The configuration defined by annotation for the handler $item, points " . 
					"to a different interface {$config->HandlerClassName}. In config: {$config->ConfigClassName}", 
				306);
		}
	}
	
	
	public function __construct()
	{
		parent::__construct(IEventConfig::class);
	}
}