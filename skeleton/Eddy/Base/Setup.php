<?php
namespace Eddy\Base\Setup;
/** @var $this \Skeleton\Base\IBoneConstructor */


use Eddy\Base\Setup\ClassName\IEventBuilder;
use Eddy\Base\Setup\ClassName\IHandlerBuilder;

use Eddy\Setup\SetupBuilder;
use Eddy\Setup\ClassNameLoader;
use Eddy\Setup\ClassName\EventBuilder;
use Eddy\Setup\ClassName\HandlerBuilder;

$this->set(IEventBuilder::class,		EventBuilder::class);
$this->set(ISetupBuilder::class,		SetupBuilder::class);
$this->set(IHandlerBuilder::class,		HandlerBuilder::class);
$this->set(IClassNameLoader::class,		ClassNameLoader::class);
