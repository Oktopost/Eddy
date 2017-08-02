<?php
namespace lib;


use Squid\MySql;


class MySQLConfig
{
	use \Objection\TStaticClass;


	public const TABLES = [
		'EddySubscribers',
		'EddyExecutors',
		'EddyEvent',
		'EddyHandler',
		'EddyStatistics',
		'EddyStatisticsSettings'
	];


	/** @var  MySql */
	private static $mysql;
	
	
	public static function get() 
	{
		return [
			'user'		=> 'root',
			'password'	=> '',
			'host'		=> 'localhost'
		];
	}
	
	public static function initTables()
	{
		$conn = new MySql\Impl\Connectors\FileConnector();
		$conn->setConnector(self::$mysql->getConnector());
		$conn->execute(__DIR__ . '/../sql/SetupEddy.sql');
	}

	/**
	 * @return MySql\IMySqlConnector
	 */
	public static function connector()
	{
		return self::$mysql->getConnector();
	}
	
	public static function clearDB()
	{
		self::$mysql->getConnector()->direct()->command('DROP DATABASE IF EXISTS _eddy_test_')->executeDml();
		self::$mysql->getConnector()->direct()->command('CREATE DATABASE IF NOT EXISTS _eddy_test_')->executeDml();
	}

	public static function setup()
	{
		self::$mysql = new MySql();
		self::$mysql->config()->setConfig(self::get());
		
		self::clearDB();
		self::$mysql->getConnector()->direct()->command('USE _eddy_test_')->executeDml();
		self::initTables();
	}
}