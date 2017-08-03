<?php
namespace Eddy\Setup;


use Eddy\Object\EventObject;
use Eddy\Object\HandlerObject;

use Eddy\Base\IEddyQueueObject;
use Eddy\Base\Setup\IClassNameLoader;


/**
 * @autoload
 */
class ClassNameLoader implements IClassNameLoader
{
	/**
	 * @autoload
	 * @var \Eddy\Base\Setup\ClassName\IEventBuilder
	 */
	private $eventBuilder;

	/**
	 * @autoload
	 * @var \Eddy\Base\Setup\ClassName\IHandlerBuilder
	 */
	private $handlerBuilder;
	
	
	public function loadEvent(string $className): ?EventObject
	{
		return $this->eventBuilder->tryBuild($className);
	}

	public function loadHandler(string $className): ?HandlerObject
	{
		return $this->handlerBuilder->tryBuild($className);
	}

	public function load(string $className): ?IEddyQueueObject
	{
		$result = $this->eventBuilder->tryBuild($className);
		return ($result ?: $this->handlerBuilder->tryBuild($className));
	}
}