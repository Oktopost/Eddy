<?php
namespace Eddy\Crawler\Base;


use Eddy\Crawler\Base\Utils\IDirectoryScanner;

class Loader implements ILoader
{
	public function load(IDirectoryScanner $scanner): array
	{
		$directoryData = $scanner->scan();
		
		var_dump($directoryData);
		die();
	}
}