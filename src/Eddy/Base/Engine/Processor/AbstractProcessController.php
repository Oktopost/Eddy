<?php
namespace Eddy\Base\Engine\Processor;


use Eddy\Base\IEddyQueueObject;
use Eddy\Object\HandlerObject;


abstract class AbstractProcessController implements IProcessController
{
	public function start(): bool {	return true; }

	public function waiting(): float { return PHP_INT_MAX; }

	public function preProcess(IEddyQueueObject $target, array $payload): void {}

	public function postProcess(IEddyQueueObject $target, array $payload): void	{}

	public function stopping(): void {}

	public function exception(HandlerObject $target, array $payload, \Throwable $t): bool {	return true; }
}