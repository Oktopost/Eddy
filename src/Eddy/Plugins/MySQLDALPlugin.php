<?php
namespace Eddy\Plugins;


use Eddy\IEddyPlugin;
use Eddy\Base\IDAL;
use Eddy\DAL\MySQLDAL;
use Eddy\Utils\Config;
use Eddy\Exceptions\UnexpectedException;

use Squid\MySql;


class MySQLDALPlugin implements IEddyPlugin
{
	/** @var IDAL */
	private $dal;


	/**
	 * @param MySql\IMySqlConnector|array $setup
	 */
	public function __construct($setup)
	{
		if (is_array($setup))
		{
			$mysql = new MySql();
			$mysql->config()->addConfig($setup);
			$setup = $mysql->getConnector();
		}
		else if (!($setup instanceof MySql\IMySqlConnector))
		{
			throw new UnexpectedException('Parameter must be array or IMySqlConnector');
		}
		
		$this->dal = new MySQLDAL($setup);
	}


	public function setup(Config $config)
	{
		$config->setDAL($this->dal);
	}
}