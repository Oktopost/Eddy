<?php
namespace Eddy\Modules;


use Eddy\Scope;
use Eddy\Base\Setup\IEventsSetup;
use Eddy\Base\Module\ISetupModule;
use Eddy\Base\Engine\Queue\IQueueObjectCreator;


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
		$queueCreator = Scope::skeleton($this, IQueueObjectCreator::class);
		
		if ($setup->Events)
		{
			$queueCreator->createQueues($setup->Events);
			$this->config->DAL()->events()->saveSetupAll($setup->Events);
		}
		
		if ($setup->Handlers)
		{
			$queueCreator->createQueues($setup->Handlers);
			$this->config->DAL()->handlers()->saveSetupAll($setup->Handlers);
		}
		
		if ($setup->Subscribers)
		{
			$this->config->DAL()->subscribers()->addSubscribersByNames($setup->Subscribers);
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