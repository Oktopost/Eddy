<?php
namespace Eddy\Enums;


class State
{
	use \Objection\TEnum;


	/**
	 * Normal event/handler operation.
	 */
	public const RUNNING	= 'running';

	/**
	 * Event/handler data will still be available, but any data transfer is stalled until the event is running again.
	 */
	public const PAUSED		= 'paused';

	/**
	 * Any event/handler data is ignored and the queue is cleared as soon as this status is applied. 
	 */
	public const STOPPED	= 'stopped';

	/**
	 * Event/handler can not be started again.
	 */
	public const DELETED	= 'deleted';
	
	
	public const EXISTING = [
		self::RUNNING,
		self::PAUSED,
		self::STOPPED
	];
}