<?php
namespace Eddy\Tests;


use Eddy\Tests\Base\ITestQueue;


class TestQueue implements ITestQueue
{
	private $map = [];
	
	
	public function getPublisher(string $className): TestPublisher
	{
		return new TestPublisher($this, $className);
	}
	
	public function publish(string $className, array $data): void
	{
		$this->map[$className] = $this->map[$className] ?? [];
		$this->map[$className] = array_merge($this->map[$className], $data);
	}
	
	public function getQueue(): array
	{
		return $this->map;
	}
	
	public function clearQueue(): void
	{
		$this->map = [];
	}
}