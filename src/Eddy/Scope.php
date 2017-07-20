<?php
namespace Eddy;


use Skeleton\Skeleton;


class Scope
{
	use \Objection\TStaticClass;
	
	
	/** @var Skeleton */
	private static $skeleton;
	
	
	/**
	 * @return mixed
	 * @return mixed|Skeleton
	 */
	public static function skeleton(?string $interface = null)
	{
		if (!self::$skeleton)
			self::$skeleton = SkeletonSetup::create();
		
		if (!$interface) 
			return self::$skeleton;
		
		return self::$skeleton->get($interface);
	}
}