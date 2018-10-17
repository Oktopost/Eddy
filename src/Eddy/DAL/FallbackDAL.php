<?php
namespace Eddy\DAL;


use Eddy\Scope;
use Eddy\Base\IDAL;
use Eddy\Base\IConfig;
use Eddy\Base\DAL\IEventDAO;
use Eddy\Base\DAL\IHandlerDAO;
use Eddy\Base\DAL\ISubscribersDAO;
use Eddy\DAL\Fallback\Base\IFallbackEventDAO;
use Eddy\DAL\Fallback\Base\IFallbackHandlerDAO;
use Eddy\DAL\Fallback\Base\IFallbackSubscribersDAO;


class FallbackDAL implements IDAL
{
	/** @var IConfig */
	private $config;
	
	/** @var IDAL */
	private $main;
	
	/** @var IDAL */
	private $fallback;
	
	/** @var IFallbackHandlerDAO */
	private $handlerDAO;

	/** @var IFallbackEventDAO */
	private $eventDAO;

	/** @var IFallbackSubscribersDAO */
	private $subscribersDAO;
	
	
	public function __construct(IDAL $mainDAL, IDAL $fallbackDAL, IConfig $config)
	{
		$this->main = $mainDAL;
		$this->fallback = $fallbackDAL;
		$this->config = $config;
	}


	public function handlers(): IHandlerDAO
	{
		if (!$this->handlerDAO)
		{
			$this->handlerDAO = Scope::skeleton(IFallbackHandlerDAO::class);
			$this->handlerDAO->setConfig($this->config);
			$this->handlerDAO->setMain($this->main->handlers());
			$this->handlerDAO->setFallback($this->fallback->handlers());
		}
		
		return $this->handlerDAO;
	}

	public function events(): IEventDAO
	{
		if (!$this->eventDAO)
		{
			$this->eventDAO = Scope::skeleton(IFallbackEventDAO::class);
			$this->eventDAO->setConfig($this->config);
			$this->eventDAO->setMain($this->main->events());
			$this->eventDAO->setFallback($this->fallback->events());
		}
		
		return $this->eventDAO;
	}

	public function subscribers(): ISubscribersDAO
	{
		if (!$this->subscribersDAO)
		{
			$this->subscribersDAO = Scope::skeleton(IFallbackSubscribersDAO::class);
			$this->subscribersDAO->setConfig($this->config);
			$this->subscribersDAO->setMain($this->main->subscribers());
			$this->subscribersDAO->setFallback($this->fallback->subscribers());
		}
		
		return $this->subscribersDAO;
	}
}