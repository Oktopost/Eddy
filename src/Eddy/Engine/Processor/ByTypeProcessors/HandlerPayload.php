<?php
namespace Eddy\Engine\Processor\ByTypeProcessors;


use Eddy\Scope;
use Eddy\Base\Module\IHandlersModule;
use Eddy\Base\Engine\Processor\ProcessTarget;
use Eddy\Base\Engine\Processor\IPayloadProcessor;
use Eddy\Object\HandlerObject;
use Eddy\Exceptions\EddyException;


/**
 * @context
 */
class HandlerPayload implements IPayloadProcessor
{
	private function pause(HandlerObject $object): void
	{
		/** @var IHandlersModule $module */
		$module = Scope::skeleton($this, IHandlersModule::class);
		$module->pause($object);
	}
	
	/**
	 * @param HandlerObject $source
	 * @return mixed
	 */
	private function getHandlerInstance(HandlerObject $source)
	{
		return Scope::skeleton()->load($source->HandlerClassName);
	}

	/**
	 * @param mixed $instance
	 * @return callable
	 */
	private function getTargetMethod($instance): callable
	{
		$reflection = new \ReflectionClass($instance);
		$methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
		
		foreach ($methods as $method)
		{
			if ($method->getNumberOfRequiredParameters() != 1) 
				continue;
			
			$param = $method->getParameters()[0];
			
			if (!$param->isArray())
				continue;
			
			return [$instance, $method->getName()];
		}
		
		throw new EddyException('Could not detect event method', 600);
	}
	
	
	public function process(ProcessTarget $target): void
	{
		/** @var HandlerObject $handler */
		$handler = $target->Object;
		
		try
		{
			$instance = $this->getHandlerInstance($handler);
			$method = $this->getTargetMethod($instance);
		}
		catch (\Throwable $t)
		{
			$this->pause($handler);
			throw $t;
		}
		
		$payload = array_values($target->Payload); 
		
		$method($payload);
	}
}