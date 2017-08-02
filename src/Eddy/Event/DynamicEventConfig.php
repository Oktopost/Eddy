<?php
namespace Eddy\Event;


use Eddy\IEventConfig;
use Eddy\ObjectAnnotations;
use Eddy\Enums\EventState;
use Eddy\Exceptions\ConfigMismatchException;

use Annotation\Value;


class DynamicEventConfig implements IEventConfig
{
	public const EVENT_SUFFIX	= 'Event';
	public const PROXY_SUFFIX	= 'Proxy';
	public const HANDLER_SUFFIX	= 'Handler';
	
	
	private $isUnique			= null;
	private $name				= null;
	private $eventClassName		= null;
	private $proxyClassName		= false;
	private $handlerClassName	= false;
	
	
	private function replaceNameSuffix(string $source, string $with = ''): string
	{
		$length = strlen(self::EVENT_SUFFIX);
		$sourceLength = strlen($source);
		
		if (substr($source, $sourceLength - $length) == self::EVENT_SUFFIX)
		{
			$source = substr($source, 0, $sourceLength - $length);
		}
		
		return $source . $with;
	}
	
	private function tryGetAnnotation(string $annotation): ?string
	{
		$value = Value::getValue($this, $annotation) ?? 
			Value::getValue($this->eventClassName(), $annotation);
		
		return $value;
	}
	
	
	public function __construct(?string $eventInterface = null)
	{
		if ($eventInterface)
		{
			$this->eventClassName = $eventInterface;
		}
	}
	

	public function delay(): float { return 0; }
	public function maxBulkSize(): int { return 256; }
	public function initialState(): string { return EventState::PAUSED; }
	

	public function name(): string
	{
		if (!$this->name)
		{
			$this->name = ObjectAnnotations::getEventName($this->eventClassName());
			
			if (!$this->name)
			{
				$this->name = ObjectAnnotations::getEventName($this);
			}
			
			if (!$this->name)
			{
				$reflection = new \ReflectionClass($this->eventClassName());
				$this->name = $reflection->getShortName();
				$this->name = $this->replaceNameSuffix($this->name());
			}
		}
		
		return $this->name;
	}

	public function prepare(array $data): ?array
	{
		if (is_null($this->isUnique))
		{
			$this->isUnique = 
				ObjectAnnotations::isUnique($this) || 
				ObjectAnnotations::isUnique($this->eventClassName());
		}
		
		return ($this->isUnique ? 
			array_unique($data) : 
			null);
	}

	public function eventClassName(): string
	{
		if (!$this->eventClassName)
			throw new ConfigMismatchException('Event interface is required', 400);
		
		return $this->eventClassName;
	}
	
	public function proxyClassName(): ?string
	{
		if ($this->proxyClassName === false)
		{
			$this->proxyClassName = null;
			$name = $this->replaceNameSuffix($this->name(), self::PROXY_SUFFIX);
			
			if (!class_exists($name))
			{
				$this->proxyClassName = $this->tryGetAnnotation(ObjectAnnotations::PROXY_ANNOTATION);
			}
		}
		
		return $this->proxyClassName;
	}

	public function handlersInterface(): string
	{
		if ($this->handlerClassName === false)
		{
			$this->handlerClassName = null;
			$name = $this->replaceNameSuffix($this->name(), self::HANDLER_SUFFIX);
			
			if (!class_exists($name))
			{
				$this->handlerClassName = $this->tryGetAnnotation(ObjectAnnotations::HANDLER_ANNOTATION);
			}
		}
		
		return $this->handlerClassName;
	}
}