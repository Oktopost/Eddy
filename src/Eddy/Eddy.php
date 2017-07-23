<?php
namespace Eddy;


use Eddy\Base\IEddyConfig;
use Eddy\Crawler\Base\ICrawler;
use Eddy\Crawler\Crawler;


class Eddy
{
	/** @var IEddyConfig */
	private $config;
	
	
	public function __construct()
	{
		$this->config = new EddyConfig();
	}


	public function config(): IEddyConfig
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