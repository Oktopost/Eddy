<?php
namespace Eddy\Modules;


use Eddy\Base\IConfig;
use Eddy\Base\Module\ISetupModule;


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
	 * @var IConfig 
	 */
	private $config;
	
	
	public function load(): void
	{
		$loaders = $this->config->Setup->Loaders;
		
		foreach ($loaders as $loader)
		{
			$items = $loader->getSetup();
			$this->builder->add($items);
		}
		
		$setup = $this->builder->get();
		
		// TODO: Save stuff
	}
}