<?php
namespace Eddy\DAL\Cached\Base;


use Eddy\Base\DAL\ICacheDAO;
use Eddy\Base\DAL\IEventDAO;


interface ICachedEventDAO extends IEventDAO
{
	public function setMain(IEventDAO $dao): void;
	public function setCache(ICacheDAO $dao): void;
}