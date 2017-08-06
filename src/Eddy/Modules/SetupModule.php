<?php
namespace Eddy\Modules;


use Eddy\Base\Module\ISetupModule;
use Eddy\Base\Setup\IEventsSetup;


/**
 * @context
 */
class SetupModule implements ISetupModule
{
	/**
	 * @autoload
	 * @var \Eddy\Base\Setup\ISetupBuilder
	 */
	private $builder;
	
	/**
	 * @context 
	 * @var \Eddy\Base\IConfig 
	 */
	private $config;
	
	
	private function save(IEventsSetup $setup): void
	{
		if ($setup->Events)
		{
			$this->config->DAL()->events()->saveSetupAll($setup->Events);
		}
		
		if ($setup->Handlers)
		{
			$this->config->DAL()->handlers()->saveSetupAll($setup->Handlers);
		}
		
		if ($setup->Subscribers)
		{
			$this->config->DAL()->subscribers()->addSubscribers($setup->Subscribers);
		}
	}
	
	
	public function load(): void
	{
		$loaders = $this->config->Setup->Loaders;
		
		foreach ($loaders as $loader)
		{
			$items = $loader->getSetup();
			$this->builder->add($items);
		}
		
		$setup = $this->builder->get();
		
		$this->save($setup);
	}
}