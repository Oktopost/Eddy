<?php
namespace Eddy\Engine\Proxy;


use Eddy\Exceptions\UnexpectedException;


class DefaultProxy extends AbstractProxy
{
	private $className;
	
	/** @var \ReflectionClass */
	private $reflection;
	
	
	public function __construct(string $decoratedClassName)
	{
		$this->className = $decoratedClassName;
	}
	
	
	public function __call($name, $arguments)
	{
		if (count($arguments) != 1)
			throw new UnexpectedException('Expecting only one parameter');
		
		$param = $arguments[0];
		
		if (!$this->reflection)
			$this->reflection = new \ReflectionClass($this->className);
		
		$method = $this->reflection->getMethod($name);
		
		if (!$method || !$method->isPublic())
			throw new UnexpectedException("The event {$this->className} does not have a method called $name");
		else if ($method->getNumberOfParameters() != 1)
			throw new UnexpectedException("The method {$this->className}::$name have an incorrect number of " . 
				'parameters. Only one allowed.');
		
		$type = $method->getParameters()[0]->getType();
		
		if (!$type || (string)$type != 'array')
		{
			$param = [$param];
		}
		else if (!is_array($param))
		{
			throw new UnexpectedException('Parameter must be an array');
		}
		
		$this->publish($param);
	}
}