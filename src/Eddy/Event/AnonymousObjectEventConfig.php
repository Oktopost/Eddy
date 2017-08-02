<?php
namespace Eddy\Event;


use Eddy\IEventConfig;
use Eddy\Enums\EventState;
use Eddy\Object\EventObject;


class AnonymousObjectEventConfig implements IEventConfig
{
	/** @var EventObject */
	private $object;
	
	
	public function __construct(EventObject $object)
	{
		$this->object = $object;
	}
	
	
	public function name(): string { return $this->object->Name; }
	public function delay(): ?float { return $this->object->Delay; }
	public function maxBulkSize(): int { return $this->object->MaxBulkSize; }
	public function initialState(): string { return EventState::RUNNING; }
	public function eventClassName(): string { return $this->object->EventInterface; }
	public function proxyClassName(): ?string { return $this->object->ProxyClassName; }
	public function handlersInterface(): string { return $this->object->HandlerInterface; }
	public function prepare(array $data): ?array { return null; }
}