<?php
namespace Eddy\Base;


use Eddy\Base\Config\IEngineConfig;
use Squid\MySql\IMySqlConnector;


/**
 * @property IEngineConfig $Engine
 */
interface IConfig
{
	public function DAL(): IDAL;
}