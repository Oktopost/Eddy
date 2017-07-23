<?php
namespace Eddy\Crawler\Base\Utils;


interface IDirectoryScanner
{
	public function setDirectory(string $directory);
	public function scan(): array;
}