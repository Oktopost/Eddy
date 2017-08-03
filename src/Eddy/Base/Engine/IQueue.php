<?php
namespace Eddy\Base\Engine;


interface IQueue
{
	public function enqueue(array $data, float $secDelay = 0.0): void;
	public function dequeue(int $maxCount, float $waitSec = 0.0): array;
}