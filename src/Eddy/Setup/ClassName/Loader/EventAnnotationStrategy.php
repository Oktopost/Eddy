<?php
namespace Eddy\Setup\ClassName\Loader;


use Eddy\IEventConfig;
use Eddy\Base\IEddyQueueObject;
use Eddy\Object\EventObject;

use Eddy\Exceptions\InterfaceMismatchConfiguration;


class EventAnnotationStrategy extends AbstractAnnotationStrategy
{
	protected function validate(string $item, IEddyQueueObject $config)
	{
		/** @var EventObject $config */
		if ($config->EventInterface != $item)
		{
			throw new InterfaceMismatchConfiguration($item, $config);
		}
	}
	
	
	public function __construct()
	{
		parent::__construct(IEventConfig::class);
	}
}