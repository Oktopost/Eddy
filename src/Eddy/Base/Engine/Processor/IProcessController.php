<?php
namespace Eddy\Base\Engine\Processor;


use Eddy\Base\IEddyQueueObject;
use Eddy\Object\HandlerObject;


interface IProcessController
{
	/**
	 * Called before each process iteration start.
	 * @return bool Return false to abort the processing.
	 */
	public function start(): bool;

	/**
	 * Called if no immediate payload is available.
	 * @return float Maximum number of seconds to wait.
	 */
	public function waiting(): float;

	/**
	 * @param IEddyQueueObject $target
	 * @param array $payload
	 */
	public function preProcess(IEddyQueueObject $target, array $payload): void;

	/**
	 * @param IEddyQueueObject $target
	 * @param array $payload
	 */
	public function postProcess(IEddyQueueObject $target, array $payload): void;

	/**
	 * Will be always called.
	 * Either if maximum run time exceeded, start() returned false, or any other
	 * internal logic caused the process to stop.
	 * This will never happen between preProcess and postProcess.
	 */
	public function stopping(): void;

	/**
	 * Called for errors in the processor of handler actions only.
	 * @param HandlerObject $target
	 * @param array $payload
	 * @param \Throwable $t
	 * @return bool Return true to proceed to the next iteration.
	 */
	public function exception(HandlerObject $target, array $payload, \Throwable $t): bool;
}