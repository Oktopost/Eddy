<?php
namespace Eddy;


use Eddy\Base\Engine\IMainQueue;
use Eddy\Base\Engine\IProcessor;
use Eddy\Base\IConfig;
use Eddy\Base\IEngine;
use Eddy\Base\Module\IEventModule;
use Eddy\Base\Module\ISetupModule;
use Eddy\Utils\Config;


class Eddy implements IEvents
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
	 * @param string $className
	 * @return mixed
	 */
	public function event(string $className)
	{
		/** @var IEventModule $eventModule */
		$eventModule = Scope::skeleton($this, IEventModule::class);

		return $this->engine->event($eventModule->loadByInterfaceName($className));
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
			
			return;
		}
		
		$result = $plugin->setup($this->config());
		
		if ($result)
		{
			$this->addPlugin($result);
		}
	}
	
	public function sendAbort(int $count = 20): void
	{
		/** @var IMainQueue $queue */
		$queue = Scope::skeleton($this, IMainQueue::class);
		$queue->sendAbort($count);
	}
	
	public function refresh(): void
	{
		/** @var IMainQueue $queue */
		$queue = Scope::skeleton($this, IMainQueue::class);
		$queue->refresh();
	}
	
	public function runSetup(): void
	{
		/** @var ISetupModule $setup */
		$setup = Scope::skeleton($this, ISetupModule::class);
		$setup->load();
	}
	
	public function handle(): void
	{
		/** @var IProcessor $processor */
		$processor = Scope::skeleton($this, IProcessor::class);
		$processor->run();
	}
}