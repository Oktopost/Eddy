<?php
namespace Eddy\Setup\ClassName\Loader;


use Eddy\IHandlerConfig;
use Eddy\Base\IEddyQueueObject;
use Eddy\Object\HandlerObject;
use Eddy\Exceptions\HandlerMismatchConfiguration;


class ByHandlerNameStrategy extends AbstractByNameStrategy
{
	protected function validate(string $item, IEddyQueueObject $config)
	{
		/** @var HandlerObject $config */
		if ($config->HandlerClassName != $item)
		{
			throw new HandlerMismatchConfiguration($item, $config);
		}
	}
	
	
	public function __construct()
	{
		parent::__construct(IHandlerConfig::class, self::HANDLER_SUFFIX);
	}
}