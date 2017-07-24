<?php
namespace Eddy\Module;


use Eddy\Object\EventObject;
use Eddy\Object\HandlerObject;
use Eddy\Base\Module\IEventHandlerModule;
use Eddy\Base\Module\DAO\IEventHandlerDAO;


/**
 * @autoload
 */
class EventHandlerModule implements IEventHandlerModule
{
	/** @var IEventHandlerDAO */
	private $dao;
	
	
	public function __construct(IEventHandlerDAO $dao)
	{
		$this->dao = $dao;
	}


	public function subscribe(EventObject $event, HandlerObject $handler): void
	{
		// TODO: Implement subscribe() method.
	}

	public function unsubscribe(EventObject $event, HandlerObject $handler): void
	{
		// TODO: Implement unsubscribe() method.
	}
}