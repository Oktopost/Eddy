<?php
namespace Eddy;


use Eddy\Base\IConfig;
use Eddy\Crawler\Base\ICrawler;
use Eddy\Crawler\Crawler;
use Eddy\Utils\Config;


class Eddy
{
	/** @var IConfig */
	private $config;
	
	
	public function __construct()
	{
		$this->config = new Config();
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
		
	}

	/**
	 * @return mixed
	 */
	public function handler(string $interface)
	{
		
	}
	
	public function crawler(): ICrawler
	{
		return new Crawler();
	}
}