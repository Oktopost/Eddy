<?php
namespace Eddy;


use Skeleton\Skeleton;
use Skeleton\ConfigLoader\DirectoryConfigLoader;


class SkeletonSetup
{
	public static function create(): Skeleton
	{
		$skeleton = new Skeleton();
		$skeleton
			->setConfigLoader(new DirectoryConfigLoader(__DIR__ . '/../../skeleton'))
			->registerGlobalFor(__NAMESPACE__)
			->useGlobal()
			->enableKnot();
		
		return $skeleton;
	}
}