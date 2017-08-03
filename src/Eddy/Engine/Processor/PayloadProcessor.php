<?php
namespace Eddy\Engine\Processor;


use Eddy\Base\IConfig;
use Eddy\Base\Engine\Processor\ProcessTarget;
use Eddy\Base\Engine\Processor\IPayloadProcessor;


/**
 * @context
 */
class PayloadProcessor implements IPayloadProcessor
{
	/**
	 * @context
	 * @var IConfig
	 */
	private $config;

	/**
	 * @autoload
	 * @var \Eddy\Base\Engine\Processor\IProcessControlChain
	 */
	private $chain;
	

	public function process(ProcessTarget $target)
	{
		$this->chain->preProcess($target->Object, $target->Payload);
		
		// TODO:
		
		$this->chain->postProcess($target->Object, $target->Payload);
	}
}