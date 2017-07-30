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
		'EddyHandler'
	];


	/** @var  MySql */
	private static $mysql;
	
	
	public static function get() 
	{
		return [
			'db'		=> '_eddy_test_',
			'user'		=> 'root',
			'password'	=> '',
			'host'		=> 'localhost'
		];
	}
	
	public static function initTables()
	{
		$tables = file_get_contents(__DIR__ . '/../../sql/SetupEddy.sql');
		
		self::$mysql->getConnector()->direct($tables)->executeDml();
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
		$conn = self::$mysql->getConnector();
		$tables = $conn->db()->listTables();
		$tables = array_filter($tables, function($value) { return in_array($value, self::TABLES); });
		
		foreach ($tables as $table)
		{
			$conn->db()->dropTable($table, false);
		}
	}

	public static function setup()
	{
		self::$mysql = new MySql();
		self::$mysql->config()->setConfig(self::get());
		self::clearDB();
		
		self::initTables();
	}
}