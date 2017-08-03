<?php
namespace Eddy\Base\Engine;


/**
 * @skeleton
 */
interface IMainQueue
{
	public function schedule(string $queueName): void;
	public function dequeue(float $waitSec = 0.0): ?string;
}