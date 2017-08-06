<?php
namespace Eddy\Base\Engine;
/** @var $this \Skeleton\Base\IBoneConstructor */


use Eddy\Engine\Processor\PayloadProcessorFactory;
use Eddy\Base\Engine\Processor\IPayloadProcessorFactory;

$this->set(IPayloadProcessorFactory::class, PayloadProcessorFactory::class);

