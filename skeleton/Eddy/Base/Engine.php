<?php
namespace Eddy\Base\Engine;
/** @var $this \Skeleton\Base\IBoneConstructor */


use Eddy\Base\Engine\Processor\IPayloadLoader;
use Eddy\Base\Engine\Processor\IPayloadProcessor;
use Eddy\Base\Engine\Processor\IIterationProcessor;
use Eddy\Base\Engine\Processor\IProcessControlChain;
use Eddy\Base\Engine\Processor\IPayloadProcessorFactory;

use Eddy\Engine\Processor\MainProcessor;
use Eddy\Engine\Processor\PayloadLoader;
use Eddy\Engine\Processor\IterationProcessor;
use Eddy\Engine\Processor\MainPayloadProcessor;
use Eddy\Engine\Processor\PayloadProcessorFactory;
use Eddy\Engine\Processor\Control\ProcessControlChain;

$this->set(IProcessor::class,				MainProcessor::class);
$this->set(IPayloadLoader::class,	 		PayloadLoader::class);
$this->set(IPayloadProcessor::class, 		MainPayloadProcessor::class);
$this->set(IIterationProcessor::class, 		IterationProcessor::class);
$this->set(IProcessControlChain::class,		ProcessControlChain::class);
$this->set(IPayloadProcessorFactory::class, PayloadProcessorFactory::class);
