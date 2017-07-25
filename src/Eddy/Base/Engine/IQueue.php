<?php
namespace Eddy\Base\Engine;


interface IQueue
{
	public function enqueue(array $data): void;
	public function dequeue(int $maxCount): array;
}