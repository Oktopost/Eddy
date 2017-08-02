<?php
namespace Eddy\Setup;


use Eddy\Object\EventObject;
use Eddy\Object\HandlerObject;

use Eddy\Base\IEddyQueueObject;
use Eddy\Base\Setup\IClassNameLoader;

use Eddy\Exceptions\ClassNameIsNotASetupObjectException;


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
	
	
	public function loadEvent(string $className): EventObject
	{
		$result = $this->eventBuilder->tryBuild($className);
		
		if (!$result)
			throw new ClassNameIsNotASetupObjectException($className, 'Target is not an Event');
		
		return $result;
	}

	public function loadHandler(string $className): HandlerObject
	{
		$result = $this->handlerBuilder->tryBuild($className);
		
		if (!$result)
			throw new ClassNameIsNotASetupObjectException($className, 'Target is not a Handler');
		
		return $result;
	}

	public function load(string $className): IEddyQueueObject
	{
		$result = $this->eventBuilder->tryBuild($className);
		
		if (!$result)
		{
			$result = $this->handlerBuilder->tryBuild($className);
		}
		
		if (!$result)
		{
			throw new ClassNameIsNotASetupObjectException($className, 'Target is not an Event or Handler');
		}
		
		return $result;
	}
}