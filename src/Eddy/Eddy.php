<?php
namespace Eddy;


use Eddy\Base\IConfig;
use Eddy\Base\IEngine;
use Eddy\Crawler\Base\ICrawler;
use Eddy\Crawler\Crawler;
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
		
		$this->engine = new Engine();
		$this->engine->setConfig($this->config);
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
		return $this->engine->event('1');
	}
	
	public function crawler(): ICrawler
	{
		return new Crawler();
	}
}