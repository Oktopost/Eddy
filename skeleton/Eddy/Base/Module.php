<?php
namespace Eddy\Base\Module;
/** @var $this \Skeleton\Base\IBoneConstructor */


use Eddy\Modules\SetupModule;
use Eddy\Modules\EventModule;
use Eddy\Modules\HandlersModule;
use Eddy\Modules\EddyObjectModule;
use Eddy\Modules\SubscribersModule;


$this->set(IEventModule::class, EventModule::class);
$this->set(ISetupModule::class, SetupModule::class);
$this->set(IHandlersModule::class, HandlersModule::class);
$this->set(IEddyObjectModule::class, EddyObjectModule::class);
$this->set(ISubscribersModule::class, SubscribersModule::class);