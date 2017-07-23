<?php
namespace Eddy\Crawler\Utils;


use Eddy\Crawler\Base\Utils\IDirectoryScanner;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use RegexIterator;


class DirectoryScanner implements IDirectoryScanner
{
	private $dir = null;
	
	
	public function setDirectory(string $directory)
	{
		$this->dir = $directory;
	}

	public function scan(): array
	{
		if (!$this->dir)
		{
			//TODO: throw exception
		}
		var_dump($this->dir);
	}
}