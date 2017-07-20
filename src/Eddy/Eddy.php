<?php
namespace Eddy;


use Eddy\Base\IEddyConfig;
use Eddy\Base\Crawler\ICrawler;
use Eddy\Engine\Base\Publisher\IPublisher;


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
		
	}
	
	public function publisher(): IPublisher
	{
		
	}
}