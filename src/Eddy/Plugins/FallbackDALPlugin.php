<?php
namespace Eddy\Plugins;


use Eddy\IEddyPlugin;
use Eddy\DAL\RedisDAL;
use Eddy\DAL\MySQLDAL;
use Eddy\DAL\CachedDAL;
use Eddy\DAL\FallbackDAL;
use Eddy\Exceptions\UnexpectedException;
use Eddy\Utils\Config;

use Predis\Client;

use Squid\MySql;


class FallbackDALPlugin implements IEddyPlugin
{
	/** @var MySql\IMySqlConnector */
	private $mySQLConnector;
	
	/** @var array */
	private $redisConfig;

	
	/**
	 * @param MySql\IMySqlConnector|array $setup
	 */
	private function getMySQLConnector($setup): MySql\IMySqlConnector
	{
		if (is_array($setup))
		{
			$mysql = new MySql();
			$mysql->config()->addConfig($setup);
			$setup = $mysql->getConnector();
		}
		else if (!($setup instanceof MySql\IMySqlConnector))
		{
			throw new UnexpectedException('MySQL setup must be array or IMySqlConnector');
		}
		
		return $setup;
	}
	

	/**
	 * @param MySql\IMySqlConnector|array $mysqlSetup
	 * @param array $redisSetup
	 */
	public function __construct($mysqlSetup, array $redisSetup = [])
	{
		$this->mySQLConnector = $this->getMySQLConnector($mysqlSetup);
		$this->redisConfig = $redisSetup;
	}


	public function setup(Config $config)
	{
		$mysqlDAL = new MySQLDAL($this->mySQLConnector);
		$redisDAL = new RedisDAL(new Client($this->redisConfig, ['prefix' => $config->Naming->MainPrefix]));
		$cachedDAL = new CachedDAL($mysqlDAL, $redisDAL);
		
		$config->setDAL(new FallbackDAL($cachedDAL, $mysqlDAL, $config));
	}
}