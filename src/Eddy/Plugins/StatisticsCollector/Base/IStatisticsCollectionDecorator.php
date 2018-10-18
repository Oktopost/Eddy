<?php
namespace Eddy\Plugins\StatisticsCollector\Base;


use Eddy\Base\Engine\Queue\IQueueDecorator;
use Eddy\Base\IConfig;


/**
 * @skeleton
 */
interface IStatisticsCollectionDecorator extends IQueueDecorator
{
	public function setConfig(IConfig $config): void;
}