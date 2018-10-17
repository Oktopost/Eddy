<?php
namespace Eddy\DAL\Fallback\Base;


use Eddy\Base\IConfig;
use Eddy\Base\DAL\IHandlerDAO;


/**
 * @skeleton
 */
interface IFallbackHandlerDAO extends IHandlerDAO
{
	public function setConfig(IConfig $config): void;
	public function setMain(IHandlerDAO $dao): void;
	public function setFallback(IHandlerDAO $dao): void;
}