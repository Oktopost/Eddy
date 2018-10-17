<?php
namespace Eddy\DAL\Fallback\Base;


use Eddy\Base\IConfig;
use Eddy\Base\DAL\IEventDAO;


/**
 * @skeleton
 */
interface IFallbackEventDAO extends IEventDAO
{
	public function setConfig(IConfig $config): void;
	public function setMain(IEventDAO $dao): void;
	public function setFallback(IEventDAO $dao): void;
}