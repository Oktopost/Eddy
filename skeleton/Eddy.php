<?php
namespace Eddy;
/** @var $this \Skeleton\Base\IBoneConstructor */


use Eddy\Crawler\Loader; 
use Eddy\Crawler\Utils\DirectoryScanner;

use Eddy\Module\EventModule;
use Eddy\Module\HandlerModule;
use Eddy\Module\EventHandlerModule;

use Eddy\DAL\MySQL\EventDAO;
use Eddy\DAL\MySQL\HandlerDAO;
use Eddy\DAL\MySQL\SubscribersDAO;

use Eddy\DAL\MySQL\Connector\EventConnector;
use Eddy\DAL\MySQL\Connector\HandlerConnector;


$this->set(Crawler\Base\ILoader::class, Loader::class);
$this->set(Crawler\Base\Utils\IDirectoryScanner::class, DirectoryScanner::class);


$this->set(Base\Module\IEventModule::class, EventModule::class);
$this->set(Base\Module\IHandlerModule::class, HandlerModule::class);
$this->set(Base\Module\IEventHandlerModule::class, EventHandlerModule::class);

$this->set(Base\DAL\IEventDAO::class, EventDAO::class);
$this->set(Base\DAL\IHandlerDAO::class, HandlerDAO::class);
$this->set(Base\DAL\ISubscribersDAO::class, SubscribersDAO::class);

$this->set(DAL\MySQL\Base\Connector\IEventConnector::class, EventConnector::class);
$this->set(DAL\MySQL\Base\Connector\IHandlerConnector::class, HandlerConnector::class);


use Eddy\Base\IEngine;
use Eddy\Base\Engine\IMainQueue;
use Eddy\Base\Engine\Queue\IQueueBuilder;
use Eddy\Base\Engine\Publish\IPublisherObject;
use Eddy\Base\Engine\Publish\IPublishBuilder;

use Eddy\Engine\Queue\MainQueue;
use Eddy\Engine\Queue\QueueBuilder;
use Eddy\Engine\Publish\PublisherObject;
use Eddy\Engine\Publish\PublishBuilder;


$this->set(IEngine::class,			Engine::class);
$this->set(IMainQueue::class,		MainQueue::class);
$this->set(IQueueBuilder::class,	QueueBuilder::class);
$this->set(IPublishBuilder::class,	PublishBuilder::class);
$this->set(IPublisherObject::class,	PublisherObject::class);