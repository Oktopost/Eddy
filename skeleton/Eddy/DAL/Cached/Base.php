<?php
namespace Eddy\DAL\Cached\Base;
/** @var $this \Skeleton\Base\IBoneConstructor */


use Eddy\DAL\Cached\CachedEventDAO;
use Eddy\DAL\Cached\CachedHandlerDAO;
use Eddy\DAL\Cached\CachedSubscribersDAO;


$this->set(ICachedEventDAO::class, CachedEventDAO::class);
$this->set(ICachedHandlerDAO::class, CachedHandlerDAO::class);
$this->set(ICachedSubscribersDAO::class, CachedSubscribersDAO::class);

