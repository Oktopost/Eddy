<?php
namespace Eddy\Utils;


use Eddy\Base\IDAL;
use Eddy\Base\IConfig;
use Eddy\Base\IExceptionHandler;
use Eddy\Exceptions\InvalidUsageException;

use Objection\LiteSetup;
use Objection\LiteObject;


class Config extends LiteObject implements IConfig
{
	/** @var IDAL|null */
	private $dal = null;
	
	
	/**
	 * @return array
	 */
	protected function _setup()
	{
		return [
			'Engine'			=> LiteSetup::createInstanceOf(EngineConfig::class),
			'Naming'			=> LiteSetup::createInstanceOf(Naming::class),
			'Setup'				=> LiteSetup::createInstanceOf(SetupConfig::class),
 			'ExceptionHandler'	=> LiteSetup::createInstanceOf(IExceptionHandler::class)
		];
	}
	
	
	public function __construct()
	{
		parent::__construct();
		$this->Engine = new EngineConfig();
		$this->Naming = new Naming();
		$this->Setup = new SetupConfig();
	}


	public function DAL(): IDAL
	{
		if (!$this->dal)
		{
			throw new InvalidUsageException('Need to setup DAL plugin before access DAL');
		}
		
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
	
	public function setDAL(IDAL $dal): void
	{
		$this->dal = $dal;
	}
}