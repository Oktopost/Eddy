<?php
namespace Eddy\Utils;


use Eddy\DAL\MySQLDAL;
use Eddy\Base\IDAL;
use Eddy\Base\IConfig;
use Eddy\Base\Config\IEngineConfig;
use Eddy\Exceptions\UnexpectedException;

use Objection\LiteObject;
use Objection\LiteSetup;

use Squid\MySql;


class Config extends LiteObject implements IConfig
{
	/** @var IDAL */
	private $dal;
	
	
	/**
	 * @return array
	 */
	protected function _setup()
	{
		return [
			'Engine'	=> LiteSetup::createInstanceOf(IEngineConfig::class),
		];
	}
	
	
	public function DAL(): IDAL
	{
		return $this->dal;
	}
	
	public function setMainDataBase($setup)
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
}