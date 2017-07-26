<?php
namespace Eddy\Base;


use Eddy\Base\Config\INaming;
use Eddy\Base\Config\IEngineConfig;


/**
 * @property IEngineConfig	$Engine
 * @property INaming		$Naming
 */
interface IConfig
{
	public function DAL(): IDAL;
}