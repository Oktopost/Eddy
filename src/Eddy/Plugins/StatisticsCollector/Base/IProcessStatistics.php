<?php
namespace Eddy\Plugins\StatisticsCollector\Base;


use Eddy\Base\IConfig;
use Eddy\Base\Engine\Processor\IProcessController;


/**
 * @skeleton
 */
interface IProcessStatistics extends IProcessController
{
	public function setConfig(IConfig $config): void;
}