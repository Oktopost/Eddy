<?php
namespace Eddy;


use Eddy\Base\IConfig;
use Eddy\Base\IEngine;
use Eddy\Base\Config\INaming;
use Eddy\Base\Module\IEventModule;
use Eddy\Utils\Config;


class Eddy
{
	/** @var IConfig */
	private $config;
	
	/** @var IEngine */
	private $engine;
	
	
	public function __construct()
	{
		$context = Scope::skeleton()->context($this, 'Eddy');
		
		$this->config = new Config();
		$context->set([
			'config'		=> $this->config,
			'names'			=> $this->config->Naming,
			INaming::class	=> $this->config->Naming
		]);
		
		$this->engine = Scope::skeleton($this, IEngine::class);
	}


	public function config(): Config
	{
		return $this->config;
	}

	/**
	 * @return mixed
	 */
	public function event(string $interface)
	{
		/** @var IEventModule $eventModule */
		$eventModule = Scope::skeleton($this, IEventModule::class);
		
		return $this->engine->event($eventModule->loadByInterfaceName($interface));
	}
}