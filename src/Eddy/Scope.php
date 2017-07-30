<?php
namespace Eddy;


use Skeleton\Skeleton;


class Scope
{
	use \Objection\TStaticClass;
	
	
	/** @var Skeleton */
	private static $skeleton;


	/**
	 * @param mixed|null $interface
	 * @param string|null $name
	 * @return mixed|Skeleton
	 */
	public static function skeleton($interface = null, string $name = null)
	{
		if (!self::$skeleton)
			self::$skeleton = SkeletonSetup::create();
		
		if (is_object($interface))
			self::$skeleton->for($interface)->get($name);
		
		if (is_string($interface)) 
			return self::$skeleton->get($interface);
		
		return self::$skeleton;
	}
}