<?php
namespace Eddy\Base;


use Eddy\Base\Config\INaming;
use Eddy\Base\Config\ISetupConfig;
use Eddy\Base\Config\IEngineConfig;


/**
 * @property IEngineConfig			$Engine
 * @property INaming				$Naming
 * @property IExceptionHandler|null	$ExceptionHandler
 * @property ISetupConfig			$Setup
 */
interface IConfig
{
	public function DAL(): IDAL;
	public function handleError(\Throwable $t): void;
	public function setMainDataBase($setup): void;
}