<?php
namespace Eddy\Base\Engine\Queue;


use Eddy\Base\Engine\IQueue;

interface IQueueProvider
{
	public function getQueue(string $name): IQueue;
}