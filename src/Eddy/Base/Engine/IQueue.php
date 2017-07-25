<?php
namespace Eddy\Base\Engine;


interface IQueue
{
	public function enqueue(array $data, ?float $secDelay): void;
	public function dequeue(int $maxCount): array;
}