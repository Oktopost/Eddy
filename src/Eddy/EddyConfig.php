<?php
namespace Eddy;


use Eddy\Base\IEddyConfig;
use Eddy\Base\Engine\IPublisher;
use Eddy\Engine\Base\Publisher\Locker\ILocker;

use DeepQueue\DeepQueue;

use Squid\MySql\IMySqlConnector;


class EddyConfig implements IEddyConfig
{
	/** @var DeepQueue */
	private $deepQueue = null;
	
	/** @var IMySqlConnector */
	private $mysqlConnector = null;
	
	/** @var ILocker */
	private $locker = null;
	
	/** @var string */
	private $prefix = '';
	
	
	public function setDeepQueue(DeepQueue $deepQueue): IEddyConfig
	{
		$this->deepQueue = $deepQueue;
		return $this;
	}

	public function setMySQL(IMySqlConnector $connector): IEddyConfig
	{
		$this->mysqlConnector = $connector;
		return $this;
	}

	public function setLocker(ILocker $locker): IEddyConfig
	{
		$this->locker = $locker;
		return $this;
	}

	public function setPrefix(?string $prefix): IEddyConfig
	{
		$this->prefix = $prefix;
		return $this;
	}

	public function publisher(): IPublisher
	{
		
	}
}