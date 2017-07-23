<?php
namespace Eddy\Crawler\Base;


use Eddy\Crawler\Base\Utils\IDirectoryScanner;

interface ILoader
{
	public function load(IDirectoryScanner $scanner): array;
}