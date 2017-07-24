<?php
namespace Eddy;
/** @var $this \Skeleton\Base\IBoneConstructor */


use Eddy\Crawler\Loader; 
use Eddy\Crawler\Utils\DirectoryScanner;


$this->set(Crawler\Base\ILoader::class, Loader::class);
$this->set(Crawler\Base\Utils\IDirectoryScanner::class, DirectoryScanner::class);