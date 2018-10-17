<?php
namespace Eddy\DAL\Fallback\Base;


use Eddy\Base\IConfig;
use Eddy\Base\DAL\ISubscribersDAO;


/**
 * @skeleton
 */
interface IFallbackSubscribersDAO extends ISubscribersDAO
{
	public function setConfig(IConfig $config): void;
	public function setMain(ISubscribersDAO $dao): void;
	public function setFallback(ISubscribersDAO $dao): void;
}