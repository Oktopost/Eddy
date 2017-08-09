<?php
namespace Eddy\DAL;


use Eddy\Scope;
use Eddy\Base\IDAL;
use Eddy\Base\DAL\IEventDAO;
use Eddy\Base\DAL\IHandlerDAO;
use Eddy\Base\DAL\ISubscribersDAO;
use Eddy\DAL\Cached\Base\ICachedEventDAO;
use Eddy\DAL\Cached\Base\ICachedHandlerDAO;
use Eddy\DAL\Cached\Base\ICachedSubscribersDAO;


class CachedDAL implements IDAL
{
	/** @var IDAL */
	private $main;

	/** @var IDAL */
	private $cache;

	/** @var IHandlerDAO */
	private $handlerDAO;

	/** @var IEventDAO */
	private $eventDAO;

	/** @var ISubscribersDAO */
	private $subscribersDAO;


	public function __construct(IDAL $mainDAL, IDAL $cacheDAL)
	{
		$this->main = $mainDAL;
		$this->cache = $cacheDAL;
	}


	public function handlers(): IHandlerDAO
	{
		if (!$this->handlerDAO) {
			$this->handlerDAO = Scope::skeleton(ICachedHandlerDAO::class);
			$this->handlerDAO->setMain($this->main->handlers());
			$this->handlerDAO->setCache($this->cache->handlers());
		}

		return $this->handlerDAO;
	}

	public function events(): IEventDAO
	{
		if (!$this->eventDAO) {
			$this->eventDAO = Scope::skeleton(ICachedEventDAO::class);
			$this->eventDAO->setMain($this->main->events());
			$this->eventDAO->setCache($this->cache->events());
		}

		return $this->eventDAO;
	}

	public function subscribers(): ISubscribersDAO
	{
		if (!$this->subscribersDAO) {
			$this->subscribersDAO = Scope::skeleton(ICachedSubscribersDAO::class);
			$this->subscribersDAO->setMain($this->main->subscribers());
			$this->subscribersDAO->setCache($this->cache->subscribers());
		}

		return $this->subscribersDAO;
	}

	public function flushCache(): void
	{
		/** @var ICachedSubscribersDAO $subscribers */
		$subscribers = $this->subscribers();
		
		/** @var ICachedHandlerDAO $handlers */
		$handlers = $this->handlers();
		
		/** @var ICachedEventDAO $events */
		$events = $this->events();
		
		$subscribers->flushAll();
		$events->flushAll();
		$handlers->flushAll();
	}
}