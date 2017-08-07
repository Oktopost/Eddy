<?php
namespace Eddy\DAL\MySQL\Base;
/** @var $this \Skeleton\Base\IBoneConstructor */


use Eddy\DAL\Redis\Base\IRedisEventDAO;
use Eddy\DAL\Redis\Base\IRedisHandlerDAO;
use Eddy\DAL\Redis\Base\IRedisSubscribersDAO;
use Eddy\DAL\Redis\RedisEventDAO;
use Eddy\DAL\Redis\RedisHandlerDAO;
use Eddy\DAL\Redis\RedisSubscribersDAO;


$this->set(IRedisEventDAO::class, RedisEventDAO::class);
$this->set(IRedisHandlerDAO::class, RedisHandlerDAO::class);
$this->set(IRedisSubscribersDAO::class, RedisSubscribersDAO::class);

