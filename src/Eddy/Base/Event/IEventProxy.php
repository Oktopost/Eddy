<?php
namespace Eddy\Base\Event;


/**
 * @eventProxy
 */
interface IEventProxy
{
	/**
	 * @param mixed $event
	 */
	public function __construct($event);
}