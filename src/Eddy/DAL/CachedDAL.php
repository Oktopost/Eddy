<?php
namespace Eddy\DAL;


use Eddy\Base\IDAL;
use Eddy\Base\DAL\IEventDAO;
use Eddy\Base\DAL\IHandlerDAO;
use Eddy\Base\DAL\ISubscribersDAO;


class CachedDAL implements IDAL
{
	public function handlers(): IHandlerDAO
	{
		// TODO: Implement handlers() method.
	}

	public function events(): IEventDAO
	{
		// TODO: Implement events() method.
	}

	public function subscribers(): ISubscribersDAO
	{
		// TODO: Implement subscribers() method.
	}
}