<?php
namespace Eddy\DAL\Cached\Base;


use Eddy\Base\DAL\ICacheDAO;
use Eddy\Base\DAL\ISubscribersDAO;


interface ICachedSubscribersDAO extends ISubscribersDAO
{
	public function setMain(ISubscribersDAO $dao): void;
	public function setCache(ICacheDAO $dao): void;
}