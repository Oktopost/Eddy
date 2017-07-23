<?php
namespace Eddy\Crawler;


use Eddy\Crawler\Base\ICrawler;
use Eddy\Crawler\Base\ILoader;
use Eddy\Crawler\Base\Utils\IDirectoryScanner;
use Eddy\Scope;


class Crawler implements ICrawler
{
	/** @var  ILoader */
	private $loader;
	
	/** @var IDirectoryScanner */
	private $directoryScanner;
	
	private function getEntities(string $dir): array
	{
		$this->directoryScanner->setDirectory($dir);
		return $this->loader->load($this->directoryScanner);
	}
	
	private function loadEvents(string $dir): array
	{
		$events = $this->getEntities($dir);
		
		return [];
	}
	
	private function loadHandlers(string $dir): array 
	{
		$handlers = $this->getEntities($dir);
		
		return [];
	}
	
	
	public function __construct()
	{
		$this->loader = Scope::skeleton(ILoader::class);
		$this->directoryScanner = Scope::skeleton(IDirectoryScanner::class);
	}


	public function load(string $eventsDir, string $handlersDir): bool
	{
		$events = $this->loadEvents($eventsDir);
		$handler = $this->loadHandlers($handlersDir);
		
		return true;
	}
}