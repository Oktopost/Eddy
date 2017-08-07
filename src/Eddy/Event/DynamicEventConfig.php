<?php
namespace Eddy\Event;


use Eddy\IEventConfig;
use Eddy\ObjectAnnotations;
use Eddy\Enums\EventState;
use Eddy\Exceptions\ConfigMismatchException;

use Annotation\Value;
use Eddy\Utils\ClassNameSearch;
use Eddy\Utils\ClassNameSearchTest;


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
	
	
	private function removeSuffix(string $source): string
	{
		$length = strlen(self::EVENT_SUFFIX);
		$sourceLength = strlen($source);
		
		if (substr($source, $sourceLength - $length) == self::EVENT_SUFFIX)
		{
			$source = substr($source, 0, $sourceLength - $length);
		}
		
		return $source;
	}
	
	private function detectClassName(string $annotation, string $suffix)
	{
		$name = $this->tryGetAnnotation($annotation);
		return ($name ?: ClassNameSearch::find($this->eventClassName(), self::EVENT_SUFFIX, $suffix));
	}
	
	private function tryGetAnnotation(string $annotation): ?string
	{
		$value = Value::getValue($this, $annotation) ?? 
			Value::getValue($this->eventClassName(), $annotation);
		
		return $value;
	}
	
	
	protected function isUnique(): bool
	{
		if (is_null($this->isUnique))
		{
			$this->isUnique = 
				ObjectAnnotations::isUnique($this) || 
				ObjectAnnotations::isUnique($this->eventClassName());
		}
		
		return $this->isUnique;
	}
	
	
	public function __construct(?string $eventInterface = null)
	{
		if ($eventInterface)
		{
			$this->eventClassName = $eventInterface;
		}
	}
	

	public function delay(): float 
	{
		return 0;
	}
	
	public function maxBulkSize(): int
	{
		return 256;
	}
	
	public function initialState(): string
	{
		return EventState::PAUSED;
	}
	

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
				$this->name = $this->removeSuffix($this->name());
			}
		}
		
		return $this->name;
	}

	public function prepare(array $data): ?array
	{
		if (!$this->isUnique() || count($data) == 1)
			return null;
		
		$unique = array_unique($data);
		
		if (count($unique) != count($data))
		{
			$data = array_values($unique);
		}
		
		return $data;
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
			$this->proxyClassName = $this->detectClassName(ObjectAnnotations::PROXY_ANNOTATION, self::PROXY_SUFFIX);
		}
		
		return $this->proxyClassName;
	}

	public function handlersInterface(): string
	{
		if ($this->handlerClassName === false)
		{
			$this->handlerClassName = $this->detectClassName(
				ObjectAnnotations::HANDLER_ANNOTATION, self::HANDLER_SUFFIX);
			
			if (!$this->handlerClassName)
				$this->handlerClassName = $this->eventClassName();
		}
		
		return $this->handlerClassName;
	}
}