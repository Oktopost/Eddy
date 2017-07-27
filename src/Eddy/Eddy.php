<?php
namespace Eddy;


use Eddy\Base\IConfig;
use Eddy\Base\IEngine;
use Eddy\Crawler\Base\ICrawler;
use Eddy\Crawler\Crawler;
use Eddy\Object\EventObject;
use Eddy\Utils\Config;


class Eddy
{
	/** @var IConfig */
	private $config;
	
	/** @var IEngine */
	private $engine;
	
	
	public function __construct()
	{
		$this->config = new Config();
		
		$context = Scope::skeleton()->context($this, 'Eddy');
		$context->set('config', $this->config);
		
		$this->engine = Scope::skeleton()->for($this)->get(IEngine::class);
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
		return $this->engine->event(new EventObject());
	}
	
	public function crawler(): ICrawler
	{
		return new Crawler();
	}
}