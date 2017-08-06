<?php
namespace Eddy;


use Eddy\Base\IConfig;
use Eddy\Base\IEngine;
use Eddy\Base\Module\IEventModule;
use Eddy\Base\Module\ISetupModule;
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
			'config' => $this->config,
			IConfig::class => $this->config
		]);

		$this->engine = Scope::skeleton($this, IEngine::class);
	}


	public function config(): Config
	{
		return $this->config;
	}

	/**
	 * @param string $interface
	 * @return mixed
	 */
	public function event(string $interface)
	{
		/** @var IEventModule $eventModule */
		$eventModule = Scope::skeleton($this, IEventModule::class);

		return $this->engine->event($eventModule->loadByInterfaceName($interface));
	}

	/**
	 * @param IEddyPlugin|IEddyPlugin[]|array $plugin
	 */
	public function addPlugin($plugin): void
	{
		if (is_array($plugin))
		{
			foreach ($plugin as $pluginItem)
			{
				$this->addPlugin($pluginItem);
			}
		}
		
		$plugin->setup($this->config());
	}
	
	public function runSetup(): void
	{
		/** @var ISetupModule $setup */
		$setup = Scope::skeleton($this, ISetupModule::class);
		$setup->load();
	}
}