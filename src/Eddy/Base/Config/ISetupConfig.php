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
	 * @param string|array $config
	 */
	public function addCrawlerSetup($config): void;
}