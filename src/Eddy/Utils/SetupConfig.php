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

	/**
	 * @param string|array $directories
	 */
	public function addCrawlerSetup($directories): void
	{
		// TODO: 
	}
}