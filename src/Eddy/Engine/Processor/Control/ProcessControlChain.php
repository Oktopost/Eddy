<?php
namespace Eddy\Engine\Processor\Control;


use Eddy\Base\Engine\Processor\IProcessControlChain;
use Eddy\Base\IEddyQueueObject;
use Eddy\Object\HandlerObject;


class ProcessControlChain implements IProcessControlChain
{
	public function count(): int
	{
		// TODO: Implement count() method.
	}

	public function start(): bool
	{
		// TODO: Implement start() method.
	}

	public function waiting(): float
	{
		// TODO: Implement waiting() method.
	}

	public function preProcess(IEddyQueueObject $target, array $payload): void
	{
		// TODO: Implement preProcess() method.
	}

	public function postProcess(IEddyQueueObject $target, array $payload): void
	{
		// TODO: Implement postProcess() method.
	}

	public function stopping(): void
	{
		// TODO: Implement stopping() method.
	}

	public function exception(HandlerObject $target, array $payload, \Throwable $t): bool
	{
		// TODO: Implement exception() method.
	}
}