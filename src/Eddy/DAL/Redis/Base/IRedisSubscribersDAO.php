<?php
namespace Eddy\DAL\Redis\Base;


use Eddy\Base\DAL\ISubscribersDAO;


interface IRedisSubscribersDAO extends ISubscribersDAO, IRedisClientConsumer
{
	
}