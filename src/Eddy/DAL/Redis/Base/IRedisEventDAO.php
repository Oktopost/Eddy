<?php
namespace Eddy\DAL\Redis\Base;


use Eddy\Base\DAL\IEventDAO;


interface IRedisEventDAO extends IEventDAO, IRedisClientConsumer
{
	
}