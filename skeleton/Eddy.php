<?php
namespace Eddy;
/** @var $this \Skeleton\Base\IBoneConstructor */


use Eddy\Crawler\Loader; 
use Eddy\Crawler\Utils\DirectoryScanner;

use Eddy\Module\EventModule;
use Eddy\Module\HandlerModule;
use Eddy\Module\EventHandlerModule;
use Eddy\Module\DAO\EventDAO;
use Eddy\Module\DAO\HandlerDAO;
use Eddy\Module\DAO\EventHandlerDAO;
use Eddy\Module\DAO\Connector\EventConnector;
use Eddy\Module\DAO\Connector\HandlerConnector;


$this->set(Crawler\Base\ILoader::class, Loader::class);
$this->set(Crawler\Base\Utils\IDirectoryScanner::class, DirectoryScanner::class);


$this->set(Base\Module\IEventModule::class, EventModule::class);
$this->set(Base\Module\IHandlerModule::class, HandlerModule::class);
$this->set(Base\Module\IEventHandlerModule::class, EventHandlerModule::class);

$this->set(Base\Module\DAO\IEventDAO::class, EventDAO::class);
$this->set(Base\Module\DAO\IHandlerDAO::class, HandlerDAO::class);
$this->set(Base\Module\DAO\IEventHandlerDAO::class, EventHandlerDAO::class);

$this->set(Base\Module\DAO\Connector\IEventConnector::class, EventConnector::class);
$this->set(Base\Module\DAO\Connector\IHandlerConnector::class, HandlerConnector::class);