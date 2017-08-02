<?php
namespace Eddy\Base\Config;


use Eddy\Base\ISetup;


/**
 * @property ISetup[] $Loaders
 */
interface ISetupConfig
{
	public function addArraySetup(array $item): void;

	/**
	 * @param string|array $directories
	 */
	public function addCrawlerSetup($directories): void;
}