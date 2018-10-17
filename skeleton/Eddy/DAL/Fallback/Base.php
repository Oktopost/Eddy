<?php
namespace Eddy\DAL\Fallback\Base;
/** @var $this \Skeleton\Base\IBoneConstructor */


use Eddy\DAL\Cached\CachedEventDAO;
use Eddy\DAL\Cached\CachedHandlerDAO;
use Eddy\DAL\Cached\CachedSubscribersDAO;
use Eddy\DAL\Fallback\FallbackEventDAO;
use Eddy\DAL\Fallback\FallbackHandlerDAO;
use Eddy\DAL\Fallback\FallbackSubscribersDAO;


$this->set(IFallbackEventDAO::class, FallbackEventDAO::class);
$this->set(IFallbackHandlerDAO::class, FallbackHandlerDAO::class);
$this->set(IFallbackSubscribersDAO::class, FallbackSubscribersDAO::class);

