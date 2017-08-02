<?php
namespace Eddy\Utils;


use Eddy\Base\Config\ISetupConfig;
use Eddy\Setup\ArraySetup;

use Objection\LiteObject;
use Objection\LiteSetup;


class SetupConfig extends LiteObject implements ISetupConfig
{
	/**
	 * @return array
	 */
	protected function _setup()
	{
		return [
			'Loaders'	=> LiteSetup::createArray()
		];
	}
	
	
	public function addArraySetup(array $item): void
	{
		$this->Loaders[] = new ArraySetup($item);
	}
	
	public function addSetup($item): void
	{
		$item = is_array($item) ? $item : [$item];
		$this->Loaders[] = new ArraySetup($item);
	}
	
	/**
	 * @param string|array|\Itarator $config
	 */
	public function addCrawlerSetup($config): void
	{
		// TODO: 
	}
}