<?php
namespace Eddy\DAL\Cached\Base;


use Eddy\Base\DAL\ICacheDAO;
use Eddy\Base\DAL\IHandlerDAO;


interface ICachedHandlerDAO extends IHandlerDAO, ICacheDAO
{
	public function setMain(IHandlerDAO $dao): void;
	public function setCache(ICacheDAO $dao): void;
}