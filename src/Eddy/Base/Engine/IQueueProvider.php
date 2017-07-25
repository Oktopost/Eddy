<?php
namespace Eddy\Base\Engine;


interface IQueueProvider
{
	public function getQueue(string $name): IQueue;
}