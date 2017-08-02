<?php
namespace Eddy\Setup\ClassName\Loader;


use Eddy\IHandlerConfig;
use Eddy\Base\IEddyQueueObject;
use Eddy\Object\HandlerObject;
use Eddy\Exceptions\ConfigMismatchException;


class ByHandlerNameStrategy extends AbstractByNameStrategy
{
	protected function validate(string $item, IEddyQueueObject $config)
	{
		/** @var HandlerObject $config */
		if ($config->HandlerClassName != $item)
		{
			throw new ConfigMismatchException(
				"The configuration defined for the handler $item, points " . 
					"to a different class {$config->HandlerClassName}. In config: {$config->ConfigClassName}", 
				302);
		}
	}
	
	
	public function __construct()
	{
		parent::__construct(IHandlerConfig::class, self::HANDLER_SUFFIX);
	}
}