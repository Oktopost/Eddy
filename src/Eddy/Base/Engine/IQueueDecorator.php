<?php
namespace Eddy\Base\Engine;


interface IQueueDecorator extends IQueue
{
	public function child(IQueue $queue): void;
}