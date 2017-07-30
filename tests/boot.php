<?php
use Eddy\Scope;

use Skeleton\UnitTestSkeleton;


require_once __DIR__ . '/../vendor/autoload.php';


class UnitTestScope
{
	/** @var UnitTestSkeleton */
	public static $unitSkeleton;
	
	
	public static function override($a, $b)
	{
		self::$unitSkeleton->override($a, $b);
	}
	
	public static function clear()
	{
		self::$unitSkeleton->clear();
	}
}


UnitTestScope::$unitSkeleton = new UnitTestSkeleton(Scope::skeleton());


require_once 'lib/MySQLConfig.php';
\lib\MySQLConfig::setup();