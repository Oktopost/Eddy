<?php
namespace Eddy\Plugins;


use Eddy\IEddyPlugin;
use Eddy\Utils\Config;

use DeepQueue\DeepQueue;


class DeepQueuePlugin implements IEddyPlugin
{
	private $deepQueue;
	
	
	public function __construct(DeepQueue $deepQueue)
	{
		$this->deepQueue = $deepQueue;
	}


	public function setup(Config $config)
	{
		$config->Engine->setQueueProvider($this->deepQueue);
	}
}