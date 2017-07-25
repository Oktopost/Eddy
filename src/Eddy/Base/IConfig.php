<?php
namespace Eddy\Base;


use Eddy\Base\Config\IEngineConfig;


/**
 * @property IEngineConfig $Engine
 */
interface IConfig
{
	public function DAL(): IDAL;
}