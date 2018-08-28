<?php
namespace Eddy\Tests\Base;


interface ITestQueue
{
	public function publish(string $className, array $data): void;
}