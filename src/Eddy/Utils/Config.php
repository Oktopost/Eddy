<?php
namespace Eddy\Utils;


use Eddy\DAL\MySQLDAL;
use Eddy\Base\IDAL;
use Eddy\Base\IConfig;
use Eddy\Base\IExceptionHandler;
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
			'Engine'			=> LiteSetup::createInstanceOf(new EngineConfig()),
			'Naming'			=> LiteSetup::createInstanceOf(new Naming()),
			'Setup'				=> LiteSetup::createInstanceOf(new SetupConfig()),
 			'ExceptionHandler'	=> LiteSetup::createInstanceOf(IExceptionHandler::class)
		];
	}
	
	
	public function DAL(): IDAL
	{
		return $this->dal;
	}
	
	public function handleError(\Throwable $t): void
	{
		if ($this->ExceptionHandler)
		{
			$this->ExceptionHandler->exception($t);
		}
		else
		{
			throw $t;
		}
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