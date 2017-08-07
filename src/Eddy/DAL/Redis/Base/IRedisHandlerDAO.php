<?php
namespace Eddy\DAL\Redis\Base;


use Eddy\Base\DAL\ICacheDAO;
use Eddy\Base\DAL\IHandlerDAO;



interface IRedisHandlerDAO extends IHandlerDAO, IRedisClientConsumer
{
	
}