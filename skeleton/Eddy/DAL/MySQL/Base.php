<?php
namespace Eddy\DAL\MySQL\Base;
/** @var $this \Skeleton\Base\IBoneConstructor */


use Eddy\DAL\MySQL\Base\Connector\IEventConnector;
use Eddy\DAL\MySQL\Base\Connector\IHandlerConnector;
use Eddy\DAL\MySQL\MySQLEventDAO;
use Eddy\DAL\MySQL\MySQLHandlerDAO;
use Eddy\DAL\MySQL\MySQLSubscribersDAO;
use Eddy\DAL\MySQL\Connector\EventConnector;
use Eddy\DAL\MySQL\Connector\HandlerConnector;


$this->set(IMySQLEventDAO::class, MySQLEventDAO::class);
$this->set(IMySQLHandlerDAO::class, MySQLHandlerDAO::class);
$this->set(IMySQLSubscribersDAO::class, MySQLSubscribersDAO::class);

$this->set(IEventConnector::class, EventConnector::class);
$this->set(IHandlerConnector::class, HandlerConnector::class);
