<?php
namespace Eddy\Utils;


use Eddy\Base\Config\IEngineConfig;
use Eddy\Base\IDAL;
use Eddy\Base\IConfig;
use Objection\LiteObject;
use Objection\LiteSetup;


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
		
	}
	
	public function setMainDataBase($setup)
	{
		
	}
}