<?php
namespace Eddy\Plugins;


use Eddy\Scope;
use Eddy\IEddyPlugin;
use Eddy\Plugins\StatisticsCollector\StatisticsCollectionDecorator;
use Eddy\Utils\Config;


class StatisticsCollectorPlugin implements IEddyPlugin
{
	public function __construct(/* configs */)
	{
		$context = Scope::skeleton()->context($this, 'Eddy::StatisticsCollectorPlugin');
		$context->set('a', 'b');
	}

	public function setup(Config $config): void
	{
		$decorator = Scope::load($this, StatisticsCollectionDecorator::class);
		$config->Engine->addDecorator($decorator);
	}
	
	
	public function dump(): void
	{
		
	}
}