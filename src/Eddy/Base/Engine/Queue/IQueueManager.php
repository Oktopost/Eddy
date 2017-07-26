<?php
namespace Eddy\Base\Engine\Queue;


interface IQueueManager
{
	public function clear();

	/**
	 * @return float|null If que is empty, null returned. Otherwise number of seconds till
	 * next payload is ready to be processed. Zero if a payload is already should be dequeued.
	 * Should not return value less then zero.
	 */
	public function getNextRuntime(): ?float;
}