<?php
namespace Eddy;
/** @var $this \Skeleton\Base\IBoneConstructor */

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


use Eddy\Plugins\StatisticsCollector\Base\IProcessStatistics;
use Eddy\Plugins\StatisticsCollector\Base\IStatisticsCacheCollector;
use Eddy\Plugins\StatisticsCollector\Base\IStatisticsCollectionDecorator;
use Eddy\Plugins\StatisticsCollector\Base\IStatisticsStorage;
use Eddy\Plugins\StatisticsCollector\ProcessStatistics;
use Eddy\Plugins\StatisticsCollector\StatisticsCollectionDecorator;
use Eddy\Plugins\StatisticsCollector\Module\RedisStatsCacheCollector;
use Eddy\Plugins\StatisticsCollector\Module\MySQLStatsStorage;

$this->set(IStatisticsStorage::class, MySQLStatsStorage::class);
$this->set(IStatisticsCacheCollector::class, RedisStatsCacheCollector::class);
$this->set(IStatisticsCollectionDecorator::class, StatisticsCollectionDecorator::class);
$this->set(IProcessStatistics::class, ProcessStatistics::class);
