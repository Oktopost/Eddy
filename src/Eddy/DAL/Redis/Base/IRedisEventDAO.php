<?php
namespace Eddy\DAL\Redis\Base;


use Eddy\Base\DAL\IEventDAO;
use Eddy\Base\DAL\ICacheDAO;


interface IRedisEventDAO extends IEventDAO, IRedisClientConsumer, ICacheDAO
{
	
}