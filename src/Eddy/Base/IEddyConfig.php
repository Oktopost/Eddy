<?php
namespace Eddy\Base;


use Eddy\Engine\Base\Publisher\IPublisher;
use Eddy\Engine\Base\Publisher\Locker\ILocker;

use DeepQueue\DeepQueue;

use Squid\MySql\IMySqlConnector;


interface IEddyConfig
{
	public function setDeepQueue(DeepQueue $deepQueue): IEddyConfig;
	public function setMySQL(IMySqlConnector $connector): IEddyConfig;
	public function setLocker(ILocker $locker): IEddyConfig;
	public function setPrefix(?string $prefix): IEddyConfig;
	
	public function publisher(): IPublisher;
}